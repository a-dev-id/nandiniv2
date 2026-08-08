<?php

namespace App\Services\Affiliate\Booking;

use App\Enums\AffiliateCommissionState;

final readonly class AffiliateCommissionEstimate
{
    public function __construct(
        public AffiliateCommissionState $state,
        public ?string $amount,
        public ?string $unavailableReason = null,
    ) {}
}
