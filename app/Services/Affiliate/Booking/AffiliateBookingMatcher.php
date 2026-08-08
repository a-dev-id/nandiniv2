<?php

namespace App\Services\Affiliate\Booking;

use App\Models\Affiliate;

class AffiliateBookingMatcher
{
    public function match(?string $code): ?Affiliate
    {
        $normalized = mb_strtolower(trim((string) $code));

        if ($normalized === '') {
            return null;
        }

        $matches = Affiliate::query()
            ->whereRaw('LOWER(affiliate_code) = ?', [$normalized])
            ->limit(2)
            ->get();

        return $matches->count() === 1 ? $matches->first() : null;
    }

    public function normalize(?string $code): ?string
    {
        $normalized = mb_strtolower(trim((string) $code));

        return $normalized === '' ? null : $normalized;
    }
}
