<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Enums\AffiliateCommissionState;
use App\Models\AffiliateBooking;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliateCommissionPeriod;
use App\Services\Affiliate\AffiliateAuditService;

class SynchronizeAffiliateCommissionItemService
{
    public function __construct(private readonly AffiliateAuditService $audit) {}

    public function synchronize(AffiliateBooking $booking): void
    {
        $item = AffiliateCommissionItem::query()->with('period', 'affiliate')->where('affiliate_booking_id', $booking->id)->lockForUpdate()->first();

        if (! $item) {
            $this->recordPendingPayment($booking);

            return;
        }

        if ($item->status->isFinanciallyLocked() || $item->period->isFinalized()) {
            $item->update([
                'source_changed_after_review' => true,
                'discrepancy_warning' => 'The source booking changed after the financial amount was locked. Finance correction is required.',
            ]);
            $this->audit->record($item->affiliate, 'affiliate_commission.source_discrepancy_detected', null, [
                'commission_item_id' => $item->id,
                'affiliate_booking_id' => $booking->id,
                'locked_status' => $item->status->value,
            ], $item);

            return;
        }

        $beforeStatus = $item->status;
        $beforeAmount = $item->approved_commission_amount;
        $targetPeriod = $this->periodFor($booking);
        $effectiveStatus = $booking->effectiveBookingStatus();
        $ineligible = $effectiveStatus->isIneligible();
        $wasReviewed = in_array($item->status, [AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::Held], true);
        $attributes = [
            'commission_period_id' => $targetPeriod->id,
            'currency' => mb_strtoupper((string) $booking->currency),
            'room_revenue_snapshot' => $booking->room_revenue_amount ?? '0.00',
            'commission_rate_snapshot' => $booking->commission_rate_snapshot,
            'original_commission_amount' => $booking->estimated_commission_amount ?? '0.00',
            'source_changed_after_review' => $wasReviewed,
            'discrepancy_warning' => $wasReviewed ? 'Source booking changed after review and requires a new Finance decision.' : null,
        ];

        if ($ineligible) {
            $attributes += [
                'status' => AffiliateCommissionItemStatus::Excluded,
                'exclusion_reason' => ($booking->manual_booking_status ? 'Booking manually marked as ' : 'Source booking changed to ').$effectiveStatus->label().'.',
                'approved_commission_amount' => null,
                'approved_at' => null,
                'approved_by' => null,
                'reviewed_at' => now(),
                'reviewed_by' => null,
            ];
        } elseif ($wasReviewed) {
            $attributes += [
                'status' => AffiliateCommissionItemStatus::PendingReview,
                'approved_commission_amount' => null,
                'adjustment_reason' => null,
                'approved_at' => null,
                'approved_by' => null,
                'reviewed_at' => null,
                'reviewed_by' => null,
            ];
        }

        $item->update($attributes);
        $this->audit->record($item->affiliate, 'affiliate_commission.source_snapshots_updated', null, [
            'commission_item_id' => $item->id,
            'affiliate_booking_id' => $booking->id,
            'previous_status' => $beforeStatus->value,
            'previous_approved_amount' => $beforeAmount,
            'new_status' => $item->status->value,
            'currency' => $item->currency,
            'original_commission_amount' => $item->original_commission_amount,
        ], $item);
    }

    private function recordPendingPayment(AffiliateBooking $booking): void
    {
        if ($booking->effectiveBookingStatus()->value !== 'completed'
            || $booking->commission_state !== AffiliateCommissionState::PendingValidation
            || $booking->room_revenue_amount === null
            || $booking->estimated_commission_amount === null
            || blank($booking->currency)) {
            return;
        }

        $period = $this->periodFor($booking);
        $item = AffiliateCommissionItem::query()->create([
            'commission_period_id' => $period->id,
            'affiliate_booking_id' => $booking->id,
            'affiliate_id' => $booking->affiliate_id,
            'currency' => mb_strtoupper($booking->currency),
            'room_revenue_snapshot' => $booking->room_revenue_amount,
            'commission_rate_snapshot' => $booking->commission_rate_snapshot,
            'original_commission_amount' => $booking->estimated_commission_amount,
            'approved_commission_amount' => $booking->estimated_commission_amount,
            'status' => AffiliateCommissionItemStatus::Approved,
            'reviewed_at' => now(),
            'approved_at' => now(),
        ]);
        $this->audit->record($booking->affiliate, 'affiliate_commission.recorded_for_payment', null, [
            'commission_period_id' => $period->id,
            'commission_item_id' => $item->id,
            'affiliate_booking_id' => $booking->id,
            'currency' => $item->currency,
            'commission_amount' => $item->approved_commission_amount,
        ], $item);
    }

    private function periodFor(AffiliateBooking $booking): AffiliateCommissionPeriod
    {
        $date = $booking->check_out_date;

        return AffiliateCommissionPeriod::query()->firstOrCreate(
            ['period_year' => $date->year, 'period_month' => $date->month],
            [
                'period_start_date' => $date->copy()->startOfMonth(),
                'period_end_date' => $date->copy()->endOfMonth(),
                'status' => AffiliateCommissionPeriodStatus::Draft,
            ],
        );
    }
}
