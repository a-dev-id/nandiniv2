<?php

namespace Database\Factories;

use App\Models\Voucher;
use App\Models\VoucherCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        $title = $this->faker->words(3, true) . ' Voucher';

        return [
            'voucher_category_id' => VoucherCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(5)),
            'sku' => Str::upper(Str::random(10)),
            'excerpt' => $this->faker->sentence(),
            'voucher_type' => 'custom',
            'selling_price' => 1000000,
            'currency' => 'IDR',
            'price_type' => 'plus_plus',
            'unit_type' => 'per_person',
            'validity_type' => 'days_after_issue',
            'validity_days' => 365,
            'minimum_quantity' => 1,
            'purchase_limit_per_order' => 10,
            'is_active' => true,
        ];
    }
}
