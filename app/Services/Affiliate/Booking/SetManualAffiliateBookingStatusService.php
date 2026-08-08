<?php

namespace App\Services\Affiliate\Booking;

use App\Enums\AffiliateBookingStatus;
use App\Models\AffiliateBooking;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use App\Services\Affiliate\Finance\SynchronizeAffiliateCommissionItemService;
use DomainException;
use Illuminate\Support\Facades\DB;

class SetManualAffiliateBookingStatusService
{
    public function __construct(
        private readonly AffiliateCommissionEstimator $commissions,
        private readonly SynchronizeAffiliateCommissionItemService $commissionItems,
        private readonly AffiliateAuditService $audit,
    ) {}

    public function set(AffiliateBooking $booking, AffiliateBookingStatus $status, string $reason, User $actor): AffiliateBooking
    {
        if (! $status->isIneligible()) {
            throw new DomainException('Manual booking outcomes must be Cancelled, No-show, or Refunded.');
        }

        $reason = trim($reason);

        if ($reason === '') {
            throw new DomainException('A reason is required for a manual booking outcome.');
        }

        return DB::transaction(function () use ($booking, $status, $reason, $actor): AffiliateBooking {
            $booking = AffiliateBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $previousStatus = $booking->manual_booking_status?->value;

            $booking->forceFill([
                'manual_booking_status' => $status,
                'manual_status_reason' => $reason,
                'manual_status_set_by' => $actor->id,
                'manual_status_set_at' => now(),
                'estimated_commission_amount' => '0.00',
                'commission_state' => $this->commissions->estimate(
                    $status,
                    $booking->room_revenue_amount,
                    $booking->currency,
                    $booking->commission_rate_snapshot,
                )->state,
                'calculation_unavailable_reason' => null,
            ])->save();

            $this->commissionItems->synchronize($booking);
            $this->audit->record($booking->affiliate, 'affiliate_booking.manual_status_set', $actor, [
                'affiliate_booking_id' => $booking->id,
                'previous_manual_status' => $previousStatus,
                'manual_status' => $status->value,
                'reason' => $reason,
            ], $booking);

            return $booking->fresh();
        });
    }

    public function clear(AffiliateBooking $booking, User $actor): AffiliateBooking
    {
        return DB::transaction(function () use ($booking, $actor): AffiliateBooking {
            $booking = AffiliateBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $previousStatus = $booking->manual_booking_status?->value;
            $estimate = $this->commissions->estimate(
                $booking->booking_status,
                $booking->room_revenue_amount,
                $booking->currency,
                $booking->commission_rate_snapshot,
            );

            $booking->forceFill([
                'manual_booking_status' => null,
                'manual_status_reason' => null,
                'manual_status_set_by' => null,
                'manual_status_set_at' => null,
                'estimated_commission_amount' => $estimate->amount,
                'commission_state' => $estimate->state,
                'calculation_unavailable_reason' => $estimate->unavailableReason,
            ])->save();

            $this->commissionItems->synchronize($booking);
            $this->audit->record($booking->affiliate, 'affiliate_booking.manual_status_cleared', $actor, [
                'affiliate_booking_id' => $booking->id,
                'previous_manual_status' => $previousStatus,
            ], $booking);

            return $booking->fresh();
        });
    }
}
