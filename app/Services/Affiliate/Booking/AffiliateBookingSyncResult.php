<?php

namespace App\Services\Affiliate\Booking;

use App\Models\AffiliateBooking;

final readonly class AffiliateBookingSyncResult
{
    /** @param  array<int, string>  $warnings */
    public function __construct(
        public string $state,
        public ?AffiliateBooking $booking = null,
        public array $warnings = [],
    ) {}
}
