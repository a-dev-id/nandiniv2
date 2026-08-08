<?php

namespace App\Services\Affiliate\Booking;

use Carbon\CarbonImmutable;

final readonly class AffiliateBookingData
{
    /**
     * @param  array<int, array<string, mixed>>  $roomItems
     */
    public function __construct(
        public string $sourceSystem,
        public string $externalBookingId,
        public ?string $externalBookingReference,
        public ?string $affiliateCode,
        public array $roomItems,
        public ?string $checkInDate,
        public ?string $checkOutDate,
        public ?string $roomRevenueAmount,
        public ?string $currency,
        public ?string $bookingStatus,
        public ?CarbonImmutable $sourceCreatedAt = null,
        public ?CarbonImmutable $sourceUpdatedAt = null,
        public ?int $syncedWebhotelierBookingId = null,
    ) {}
}
