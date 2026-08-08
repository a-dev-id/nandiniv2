<?php

namespace App\Services\Affiliate\Click;

use App\Models\Affiliate;
use App\Models\AffiliateClickEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AffiliateClickAnalyticsService
{
    public const RANGES = ['7', '30', '90', 'all'];

    /** @var array<string, array<string, mixed>> */
    private array $affiliateCache = [];

    /** @return array<string, mixed> */
    public function forAffiliate(Affiliate $affiliate, string $range = '30'): array
    {
        $range = $this->validRange($range);
        $cacheKey = $affiliate->getKey().':'.$range;

        if (isset($this->affiliateCache[$cacheKey])) {
            return $this->affiliateCache[$cacheKey];
        }

        $query = $this->publicEvents($range)->where('affiliate_id', $affiliate->getKey());
        $total = (clone $query)->count();
        $unique = (clone $query)->where('is_unique', true)->count();
        $countries = $this->countryBreakdown(clone $query, $total);
        $devices = $this->deviceBreakdown(clone $query, $total);
        $topCountry = $countries->first(fn (array $row): bool => $row['code'] !== null);
        $topDevice = $devices->first();
        $lastClick = (clone $query)->max('clicked_at');

        return $this->affiliateCache[$cacheKey] = [
            'range' => $range,
            'summary' => [
                'total' => $total,
                'unique' => $unique,
                'this_month' => AffiliateClickEvent::query()
                    ->where('affiliate_id', $affiliate->getKey())
                    ->where('is_bot', false)
                    ->whereBetween('click_date', [now()->startOfMonth()->toDateString(), now()->toDateString()])
                    ->count(),
                'top_country' => $topCountry['country'] ?? null,
                'top_device' => $topDevice['device'] ?? null,
                'last_click' => $lastClick,
                'bots' => $this->events($range)->where('affiliate_id', $affiliate->getKey())->where('is_bot', true)->count(),
            ],
            'countries' => $countries->values()->all(),
            'devices' => $devices->values()->all(),
            'trend' => $this->trend($affiliate, $range),
        ];
    }

    /** @return array<string, mixed> */
    public function overview(string $range = '30', ?int $affiliateId = null, ?string $status = null): array
    {
        $range = $this->validRange($range);
        $public = $this->publicEvents($range);
        $all = $this->events($range);

        if ($affiliateId) {
            $public->where('affiliate_id', $affiliateId);
            $all->where('affiliate_id', $affiliateId);
        }

        if ($status) {
            $public->whereHas('affiliate', fn (Builder $query) => $query->where('status', $status));
            $all->whereHas('affiliate', fn (Builder $query) => $query->where('status', $status));
        }

        $total = (clone $public)->count();
        $topAffiliates = (clone $public)
            ->join('affiliates', 'affiliates.id', '=', 'affiliate_click_events.affiliate_id')
            ->select([
                'affiliates.id',
                'affiliates.name',
                'affiliates.affiliate_code',
                DB::raw('COUNT(affiliate_click_events.id) as total_clicks'),
                DB::raw('SUM(CASE WHEN affiliate_click_events.is_unique = 1 THEN 1 ELSE 0 END) as unique_clicks'),
                DB::raw('MAX(affiliate_click_events.clicked_at) as last_click'),
            ])
            ->groupBy('affiliates.id', 'affiliates.name', 'affiliates.affiliate_code')
            ->orderByDesc('total_clicks')
            ->limit(10)
            ->get();

        $topCountriesByAffiliate = collect();
        $affiliateIds = $topAffiliates->pluck('id')->all();

        if ($affiliateIds !== []) {
            $countryRows = $this->publicEvents($range)
                ->whereIn('affiliate_id', $affiliateIds)
                ->whereNotNull('country_code')
                ->select('affiliate_id', 'country_name', DB::raw('COUNT(*) as clicks'))
                ->groupBy('affiliate_id', 'country_name')
                ->orderByDesc('clicks')
                ->get();

            $topCountriesByAffiliate = $countryRows->groupBy('affiliate_id')->map->first();
        }

        return [
            'range' => $range,
            'summary' => [
                'total' => $total,
                'unique' => (clone $public)->where('is_unique', true)->count(),
                'bots' => (clone $all)->where('is_bot', true)->count(),
            ],
            'countries' => $this->countryBreakdown(clone $public, $total)->values()->all(),
            'devices' => $this->deviceBreakdown(clone $public, $total)->values()->all(),
            'top_affiliates' => $topAffiliates->map(fn ($row): array => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'code' => $row->affiliate_code,
                'total' => (int) $row->total_clicks,
                'unique' => (int) $row->unique_clicks,
                'top_country' => $topCountriesByAffiliate->get($row->id)?->country_name,
                'last_click' => $row->last_click,
            ])->all(),
        ];
    }

    private function validRange(string $range): string
    {
        return in_array($range, self::RANGES, true) ? $range : '30';
    }

    private function events(string $range): Builder
    {
        $query = AffiliateClickEvent::query();

        if ($range !== 'all') {
            $query->whereBetween('click_date', [
                now()->subDays(((int) $range) - 1)->toDateString(),
                now()->toDateString(),
            ]);
        }

        return $query;
    }

    private function publicEvents(string $range): Builder
    {
        return $this->events($range)->where('is_bot', false);
    }

    /** @return Collection<int, array{code: ?string, country: string, clicks: int, percentage: float}> */
    private function countryBreakdown(Builder $query, int $total): Collection
    {
        return $query
            ->select('country_code', 'country_name', DB::raw('COUNT(*) as clicks'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get()
            ->map(fn ($row): array => [
                'code' => $row->country_code,
                'country' => $row->country_name ?: 'Unknown',
                'clicks' => (int) $row->clicks,
                'percentage' => $total > 0 ? round(((int) $row->clicks / $total) * 100, 1) : 0.0,
            ]);
    }

    /** @return Collection<int, array{device: string, label: string, clicks: int, percentage: float}> */
    private function deviceBreakdown(Builder $query, int $total): Collection
    {
        return $query
            ->select('device_type', DB::raw('COUNT(*) as clicks'))
            ->groupBy('device_type')
            ->orderByDesc('clicks')
            ->get()
            ->map(fn ($row): array => [
                'device' => $row->device_type,
                'label' => ucfirst($row->device_type),
                'clicks' => (int) $row->clicks,
                'percentage' => $total > 0 ? round(((int) $row->clicks / $total) * 100, 1) : 0.0,
            ]);
    }

    /** @return array<int, array{label: string, total: int, unique: int}> */
    private function trend(Affiliate $affiliate, string $range): array
    {
        $query = $this->publicEvents($range)
            ->where('affiliate_id', $affiliate->getKey())
            ->select('click_date', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_unique = 1 THEN 1 ELSE 0 END) as unique_clicks'))
            ->groupBy('click_date')
            ->orderBy('click_date');
        $rows = $query->get()->keyBy(fn ($row): string => CarbonImmutable::parse($row->click_date)->toDateString());

        if ($range === 'all') {
            if ($rows->isEmpty()) {
                return [];
            }

            $first = CarbonImmutable::parse((string) $rows->keys()->first());
            $last = CarbonImmutable::parse((string) $rows->keys()->last());

            if ($first->diffInDays($last) > 120) {
                return $rows->groupBy(fn ($row): string => CarbonImmutable::parse($row->click_date)->format('Y-m'))
                    ->map(fn (Collection $month, string $key): array => [
                        'label' => CarbonImmutable::createFromFormat('Y-m', $key)->format('M Y'),
                        'total' => (int) $month->sum('total'),
                        'unique' => (int) $month->sum('unique_clicks'),
                    ])->values()->all();
            }

            $days = $first->diffInDays($last) + 1;
        } else {
            $days = (int) $range;
            $first = CarbonImmutable::instance(now()->subDays($days - 1))->startOfDay();
        }

        return collect(range(0, $days - 1))->map(function (int $offset) use ($first, $rows): array {
            $date = $first->addDays($offset);
            $row = $rows->get($date->toDateString());

            return [
                'label' => $date->format('d M Y'),
                'total' => (int) ($row?->total ?? 0),
                'unique' => (int) ($row?->unique_clicks ?? 0),
            ];
        })->all();
    }
}
