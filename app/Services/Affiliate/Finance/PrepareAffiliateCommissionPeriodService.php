<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Enums\AffiliateCommissionState;
use App\Models\AffiliateBooking;
use App\Models\AffiliateCommissionPeriod;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrepareAffiliateCommissionPeriodService
{
    public function __construct(private readonly AffiliateAuditService $audit) {}

    /** @return array{period: AffiliateCommissionPeriod, created: int, unchanged: int, skipped: int, unavailable: int} */
    public function prepare(int $year, int $month, ?User $actor = null): array
    {
        if ($year < 2000 || $year > 2200 || $month < 1 || $month > 12) {
            throw ValidationException::withMessages(['period' => 'Enter a valid commission year and month.']);
        }

        $start = CarbonImmutable::create($year, $month, 1, 0, 0, 0, config('app.timezone'))->startOfMonth();
        $end = $start->endOfMonth();

        return DB::transaction(function () use ($year, $month, $start, $end, $actor): array {
            $period = AffiliateCommissionPeriod::query()->firstOrCreate(
                ['period_year' => $year, 'period_month' => $month],
                [
                    'period_start_date' => $start->toDateString(),
                    'period_end_date' => $end->toDateString(),
                    'status' => AffiliateCommissionPeriodStatus::Draft,
                ],
            );
            $wasCreated = $period->wasRecentlyCreated;

            if ($period->isFinalized()) {
                throw ValidationException::withMessages(['period' => 'A finalized commission period cannot be prepared again unless it is reopened.']);
            }

            $base = AffiliateBooking::query()
                ->where('booking_status', AffiliateBookingStatus::Completed)
                ->whereDate('check_out_date', '>=', $start->toDateString())
                ->whereDate('check_out_date', '<=', $end->toDateString());

            $unchanged = (clone $base)->whereHas('commissionItem')->count();
            $skipped = AffiliateBooking::query()
                ->whereDate('check_out_date', '>=', $start->toDateString())
                ->whereDate('check_out_date', '<=', $end->toDateString())
                ->where(function ($query): void {
                    $query->whereIn('booking_status', [
                        AffiliateBookingStatus::Cancelled,
                        AffiliateBookingStatus::NoShow,
                        AffiliateBookingStatus::Refunded,
                    ])->orWhere('commission_state', '!=', AffiliateCommissionState::PendingValidation);
                })->count();
            $created = 0;
            $unavailable = 0;

            (clone $base)
                ->where('commission_state', AffiliateCommissionState::PendingValidation)
                ->whereDoesntHave('commissionItem')
                ->with('affiliate')
                ->lockForUpdate()
                ->each(function (AffiliateBooking $booking) use ($period, $actor, &$created, &$unavailable): void {
                    if ($booking->room_revenue_amount === null || $booking->estimated_commission_amount === null || blank($booking->currency)) {
                        $unavailable++;

                        return;
                    }

                    $item = $period->items()->create([
                        'affiliate_booking_id' => $booking->id,
                        'affiliate_id' => $booking->affiliate_id,
                        'currency' => mb_strtoupper($booking->currency),
                        'room_revenue_snapshot' => $booking->room_revenue_amount,
                        'commission_rate_snapshot' => $booking->commission_rate_snapshot,
                        'original_commission_amount' => $booking->estimated_commission_amount,
                        'status' => AffiliateCommissionItemStatus::PendingReview,
                    ]);
                    $this->audit->record($booking->affiliate, 'affiliate_commission_item.prepared', $actor, [
                        'commission_period_id' => $period->id,
                        'commission_item_id' => $item->id,
                        'affiliate_booking_id' => $booking->id,
                        'currency' => $item->currency,
                        'original_commission_amount' => $item->original_commission_amount,
                    ], $item);
                    $created++;
                });

            $period->forceFill([
                'status' => $period->status === AffiliateCommissionPeriodStatus::Reopened
                    ? AffiliateCommissionPeriodStatus::Reopened
                    : AffiliateCommissionPeriodStatus::UnderReview,
                'prepared_at' => now(),
                'prepared_by' => $actor?->getKey(),
            ])->save();

            if ($wasCreated) {
                $this->audit->record(null, 'affiliate_commission_period.created', $actor, [
                    'period_year' => $year,
                    'period_month' => $month,
                ], $period);
            }

            return compact('period', 'created', 'unchanged', 'skipped', 'unavailable');
        });
    }
}
