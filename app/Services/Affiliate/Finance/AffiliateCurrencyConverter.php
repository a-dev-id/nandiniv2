<?php

namespace App\Services\Affiliate\Finance;

use App\Models\AffiliateExchangeRate;
use Illuminate\Validation\ValidationException;

class AffiliateCurrencyConverter
{
    public function convert(string $amount, string $from, string $to, bool $required = true): ?array
    {
        $from = mb_strtoupper($from);
        $to = mb_strtoupper($to);

        if ($from === $to) {
            return ['amount' => bcadd($amount, '0', 2), 'rate' => '1.000000'];
        }

        $rate = AffiliateExchangeRate::query()
            ->where('base_currency', $from)
            ->where('quote_currency', $to)
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('effective_at')->orWhere('effective_at', '<=', now()))
            ->first();

        if (! $rate) {
            if (! $required) {
                return null;
            }

            throw ValidationException::withMessages([
                'exchange_rate' => "Add an active {$from} to {$to} Affiliate exchange rate before recording this payment.",
            ]);
        }

        return [
            'amount' => number_format(round((float) $amount / (float) $rate->base_units_per_quote, 2), 2, '.', ''),
            'rate' => (string) $rate->base_units_per_quote,
        ];
    }
}
