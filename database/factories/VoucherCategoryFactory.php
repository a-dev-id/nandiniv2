<?php

namespace Database\Factories;

use App\Models\VoucherCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoucherCategoryFactory extends Factory
{
    protected $model = VoucherCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(5)),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
