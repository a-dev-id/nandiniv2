<?php

namespace App\Services\Affiliate\Reports;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final readonly class AffiliateReportDateRange
{
    public function __construct(public CarbonImmutable $from, public CarbonImmutable $to, public string $preset) {}

    public static function fromRequest(Request $request): self
    {
        $validated = $request->validate([
            'range' => ['nullable', Rule::in(['this_month', 'last_month', 'last_3_months', 'this_year', 'custom'])],
            'from' => ['nullable', 'required_if:range,custom', 'date_format:Y-m-d'],
            'to' => ['nullable', 'required_if:range,custom', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $preset = $validated['range'] ?? 'this_month';
        $now = CarbonImmutable::now(config('app.timezone'));

        [$from, $to] = match ($preset) {
            'last_month' => [$now->subMonthNoOverflow()->startOfMonth(), $now->subMonthNoOverflow()->endOfMonth()],
            'last_3_months' => [$now->subMonthsNoOverflow(2)->startOfMonth(), $now->endOfDay()],
            'this_year' => [$now->startOfYear(), $now->endOfDay()],
            'custom' => [CarbonImmutable::parse($validated['from'], config('app.timezone'))->startOfDay(), CarbonImmutable::parse($validated['to'], config('app.timezone'))->endOfDay()],
            default => [$now->startOfMonth(), $now->endOfDay()],
        };

        abort_if($from->diffInDays($to) > 731, 422, 'The report range may not exceed two years.');

        return new self($from, $to, $preset);
    }

    public function query(): array
    {
        return ['range' => $this->preset, 'from' => $this->from->toDateString(), 'to' => $this->to->toDateString()];
    }
}
