<?php

namespace App\Services\Affiliate\Finance;

use Illuminate\Validation\ValidationException;

class DecimalMoney
{
    public function normalize(mixed $value, bool $allowNegative = false, string $field = 'amount'): string
    {
        $value = trim((string) $value);
        $pattern = $allowNegative ? '/^-?\d{1,13}(?:\.\d{1,2})?$/' : '/^\d{1,13}(?:\.\d{1,2})?$/';

        if (! preg_match($pattern, $value)) {
            throw ValidationException::withMessages([$field => 'Enter a valid monetary amount with no more than two decimal places.']);
        }

        return bcadd($value, '0', 2);
    }

    /** @param iterable<int, mixed> $amounts */
    public function sum(iterable $amounts): string
    {
        $total = '0.00';

        foreach ($amounts as $amount) {
            $total = bcadd($total, (string) $amount, 2);
        }

        return $total;
    }
}
