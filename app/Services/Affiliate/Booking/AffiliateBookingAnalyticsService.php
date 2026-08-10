<?php

namespace App\Services\Affiliate\Booking;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateBookingRoom;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AffiliateBookingAnalyticsService
{
    public const FILTERS = ['upcoming', 'completed', 'ineligible', 'all'];

    /** @var array<int, array<string, mixed>> */
    private array $summaryCache = [];

    /**
     * @return array{summary: array{tracked_bookings: int, room_nights: int, commission_totals: array<int, array{currency: string, amount: string}>}, bookings: LengthAwarePaginator}
     */
    public function forAffiliate(Affiliate $affiliate, string $filter = 'upcoming'): array
    {
        $filter = in_array($filter, self::FILTERS, true) ? $filter : 'upcoming';
        $base = AffiliateBooking::query()->where('affiliate_id', $affiliate->id);
        $summary = $this->summaryForAffiliate($affiliate);

        $query = (clone $base)->with('rooms');
        $this->applyFilter($query, $filter);

        return [
            'summary' => $summary,
            'bookings' => $query
                ->paginate(10, ['*'], 'booking_page')
                ->withQueryString(),
        ];
    }

    /** @return array{tracked_bookings: int, upcoming_stays: int, completed_stays: int, room_nights: int, commission_totals: array<int, array{currency: string, amount: string}>, commission_states: array<string, int>, last_synced_at: mixed} */
    public function summaryForAffiliate(Affiliate $affiliate): array
    {
        if (isset($this->summaryCache[$affiliate->id])) {
            return $this->summaryCache[$affiliate->id];
        }

        $base = AffiliateBooking::query()->where('affiliate_id', $affiliate->id);
        $commissionTotals = (clone $base)
            ->where('commission_state', AffiliateCommissionState::Estimated->value)
            ->whereNotNull('estimated_commission_amount')
            ->whereNotNull('currency')
            ->select('currency', DB::raw('SUM(estimated_commission_amount) as amount'))
            ->groupBy('currency')
            ->orderBy('currency')
            ->get()
            ->map(fn (AffiliateBooking $booking): array => [
                'currency' => (string) $booking->currency,
                'amount' => (string) $booking->getRawOriginal('amount'),
            ])
            ->all();

        $roomNights = (int) AffiliateBookingRoom::query()
            ->whereHas('booking', fn (Builder $query): Builder => $query->where('affiliate_id', $affiliate->id))
            ->sum(DB::raw('stay_nights * room_quantity'));

        $commissionStates = (clone $base)
            ->select('commission_state', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('commission_state')
            ->pluck('aggregate', 'commission_state')
            ->map(fn ($count): int => (int) $count)
            ->all();

        return $this->summaryCache[$affiliate->id] = [
            'tracked_bookings' => (clone $base)->count(),
            'upcoming_stays' => (clone $base)->whereIn('booking_status', [
                AffiliateBookingStatus::Confirmed->value,
                AffiliateBookingStatus::InHouse->value,
            ])->count(),
            'completed_stays' => (clone $base)->where('booking_status', AffiliateBookingStatus::Completed->value)->count(),
            'room_nights' => $roomNights,
            'commission_totals' => $commissionTotals,
            'commission_states' => $commissionStates,
            'last_synced_at' => (clone $base)->max('last_synced_at'),
        ];
    }

    private function applyFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'upcoming' => $query
                ->whereIn('booking_status', [
                    AffiliateBookingStatus::Confirmed->value,
                    AffiliateBookingStatus::InHouse->value,
                ])
                ->orderBy('check_in_date'),
            'completed' => $query
                ->where('booking_status', AffiliateBookingStatus::Completed->value)
                ->orderByDesc('check_out_date'),
            'ineligible' => $query
                ->whereIn('booking_status', [
                    AffiliateBookingStatus::Cancelled->value,
                    AffiliateBookingStatus::NoShow->value,
                    AffiliateBookingStatus::Refunded->value,
                ])
                ->orderByDesc('check_out_date'),
            default => $query->orderByDesc('check_out_date'),
        };
    }
}
