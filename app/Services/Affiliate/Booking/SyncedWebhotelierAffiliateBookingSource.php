<?php

namespace App\Services\Affiliate\Booking;

use App\Models\SyncedWebhotelierBooking;
use Carbon\CarbonImmutable;

class SyncedWebhotelierAffiliateBookingSource implements AffiliateBookingSource
{
    public function normalize(SyncedWebhotelierBooking $booking): AffiliateBookingData
    {
        $roomRevenue = $booking->room_subtotal !== null
            ? (string) $booking->room_subtotal
            : null;

        return new AffiliateBookingData(
            sourceSystem: 'membership_booking_api',
            externalBookingId: (string) $booking->booking_number,
            externalBookingReference: (string) $booking->booking_number,
            affiliateCode: $booking->affiliate_code,
            roomItems: [[
                'external_room_id' => 'primary',
                'room_type_name' => $booking->room_name ?: $booking->room_type ?: 'Room details unavailable',
                'room_quantity' => max(1, (int) ($booking->rooms ?: 1)),
                'room_revenue_amount' => $roomRevenue,
                'currency' => $booking->currency,
            ]],
            checkInDate: $booking->check_in?->toDateString(),
            checkOutDate: $booking->check_out?->toDateString(),
            roomRevenueAmount: $roomRevenue,
            currency: $booking->currency,
            bookingStatus: $booking->status,
            sourceCreatedAt: $booking->created_at ? CarbonImmutable::instance($booking->created_at) : null,
            sourceUpdatedAt: $booking->remote_updated_at
                ? CarbonImmutable::instance($booking->remote_updated_at)
                : ($booking->last_synced_at ? CarbonImmutable::instance($booking->last_synced_at) : null),
            syncedWebhotelierBookingId: $booking->getKey(),
        );
    }
}
