<?php

namespace App\Services\Affiliate\Booking;

class AffiliateMoneyFormatter
{
    public function format(int|string|null $amount, ?string $currency): string
    {
        $currency = mb_strtoupper(trim((string) $currency)) ?: '—';
        $decimals = $currency === 'IDR' ? 0 : 2;

        return $currency.' '.number_format((float) ($amount ?? 0), $decimals, '.', ',');
    }
}
