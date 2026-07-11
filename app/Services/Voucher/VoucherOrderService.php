<?php

namespace App\Services\Voucher;

use App\Models\VoucherOrder;
use Illuminate\Support\Str;

class VoucherOrderService
{
    public function makeOrderNumber(): string
    {
        do {
            $number = 'NDN-VCH-' . now()->format('ymd') . '-' . strtoupper(Str::random(6));
        } while (VoucherOrder::query()->where('order_number', $number)->exists());

        return $number;
    }
}
