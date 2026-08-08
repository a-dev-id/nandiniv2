<?php

namespace App\Services\Affiliate;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class AffiliateCodeGenerator
{
    public function base(string $name, CarbonInterface $registeredAt): string
    {
        $normalizedName = preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii($name)));
        $normalizedName = $normalizedName !== '' ? $normalizedName : 'partner';

        return $normalizedName.$registeredAt->day.$registeredAt->month.$registeredAt->format('y');
    }

    public function candidate(string $base, int $attempt): string
    {
        return $attempt === 1 ? $base : $base.str_pad((string) $attempt, 2, '0', STR_PAD_LEFT);
    }
}
