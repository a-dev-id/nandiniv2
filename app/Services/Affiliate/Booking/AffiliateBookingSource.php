<?php

namespace App\Services\Affiliate\Booking;

use App\Models\SyncedWebhotelierBooking;

interface AffiliateBookingSource
{
    public function normalize(SyncedWebhotelierBooking $booking): AffiliateBookingData;
}
