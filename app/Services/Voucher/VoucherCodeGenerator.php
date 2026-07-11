<?php

namespace App\Services\Voucher;

use Illuminate\Support\Str;

class VoucherCodeGenerator
{
    public function code(string $type = 'custom'): string
    {
        $prefix = match ($type) {
            'spa' => 'NDN-SPA',
            'accommodation' => 'NDN-STAY',
            'dining' => 'NDN-DINE',
            'experience' => 'NDN-EXP',
            'monetary' => 'NDN-GIFT',
            default => 'NDN-VCH',
        };

        return $prefix . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    public function verificationToken(): string
    {
        return Str::random(64);
    }
}
