<?php

namespace App\Services\Affiliate\Booking;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;

class AffiliateCommissionEstimator
{
    public function estimate(
        AffiliateBookingStatus $status,
        ?string $roomRevenue,
        ?string $currency,
        string $commissionRate,
    ): AffiliateCommissionEstimate {
        if ($status->isIneligible()) {
            return new AffiliateCommissionEstimate(AffiliateCommissionState::Ineligible, '0.00');
        }

        if ($status === AffiliateBookingStatus::Unknown) {
            return new AffiliateCommissionEstimate(
                AffiliateCommissionState::CalculationUnavailable,
                null,
                'Unknown booking status.',
            );
        }

        if ($roomRevenue === null) {
            return new AffiliateCommissionEstimate(
                AffiliateCommissionState::CalculationUnavailable,
                null,
                'Verified room revenue is unavailable from the booking source.',
            );
        }

        if (blank($currency)) {
            return new AffiliateCommissionEstimate(
                AffiliateCommissionState::CalculationUnavailable,
                null,
                'Booking currency is unavailable.',
            );
        }

        $unrounded = bcdiv(bcmul($roomRevenue, $commissionRate, 6), '100', 6);
        $amount = bcadd($unrounded, '0.005', 2);

        if (bccomp($amount, '9999999999999.99', 2) > 0) {
            return new AffiliateCommissionEstimate(
                AffiliateCommissionState::CalculationUnavailable,
                null,
                'Calculated commission exceeds the supported amount.',
            );
        }
        $state = $status === AffiliateBookingStatus::Completed
            ? AffiliateCommissionState::PendingValidation
            : AffiliateCommissionState::Estimated;

        return new AffiliateCommissionEstimate($state, $amount);
    }
}
