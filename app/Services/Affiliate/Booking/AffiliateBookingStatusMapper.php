<?php

namespace App\Services\Affiliate\Booking;

use App\Enums\AffiliateBookingStatus;

class AffiliateBookingStatusMapper
{
    public function map(?string $sourceStatus): AffiliateBookingStatus
    {
        return match ($this->normalize($sourceStatus)) {
            'reserved', 'confirmed', 'booked' => AffiliateBookingStatus::Confirmed,
            'checked_in', 'checkedin', 'inhouse', 'in_house' => AffiliateBookingStatus::InHouse,
            'checked_out', 'checkedout', 'completed', 'departed' => AffiliateBookingStatus::Completed,
            'cancelled', 'canceled' => AffiliateBookingStatus::Cancelled,
            'no_show', 'noshow' => AffiliateBookingStatus::NoShow,
            'refunded', 'fully_refunded' => AffiliateBookingStatus::Refunded,
            default => AffiliateBookingStatus::Unknown,
        };
    }

    public function normalize(?string $sourceStatus): ?string
    {
        $status = mb_strtolower(trim((string) $sourceStatus));
        $status = preg_replace('/[^a-z0-9]+/', '_', $status) ?? $status;
        $status = trim($status, '_');

        return $status === '' ? null : mb_substr($status, 0, 100);
    }
}
