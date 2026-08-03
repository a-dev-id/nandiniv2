<?php

namespace App\Services\Voucher;

class MoneyFormatter
{
    public function format(int|string|null $amount, string $currency = 'IDR'): string
    {
        $amount = (int) $amount;

        return $currency . ' ' . number_format($amount, 0, '.', ',');
    }

    public function priceTypeSuffix(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        return match ($value) {
            '++', 'plus_plus' => '++',
            'net', 'nett' => ' Net',
            'inclusive' => ' Inclusive',
            default => '',
        };
    }

    public function unitLabel(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return match ($value) {
            'per_person' => 'Per Person',
            'per_couple' => 'Per Couple',
            'per_booking' => 'Per Booking',
            default => str($value)->replace('_', ' ')->title()->toString(),
        };
    }
}
