<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\VoucherCategory;
use App\Services\Voucher\ExperienceVoucherSynchronizer;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::query()
            ->whereIn('slug', [
                'monetary-gift-voucher',
                'romantic-dining-voucher',
                'spa-experience-voucher',
                'jungle-stay-voucher',
                'panoramic-jacuzzi-royal-suite-voucher',
                'nandini-experience-voucher',
            ])
            ->update(['is_active' => false, 'is_featured' => false]);

        VoucherCategory::query()
            ->whereIn('slug', ['stay', 'gift'])
            ->update(['is_active' => false]);

        app(ExperienceVoucherSynchronizer::class)->synchronize();
    }
}
