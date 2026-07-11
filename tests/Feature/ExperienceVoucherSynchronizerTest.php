<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\ExperiencePrice;
use App\Models\Voucher;
use App\Services\Voucher\ExperienceVoucherSynchronizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceVoucherSynchronizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_the_linked_voucher_and_preserves_voucher_sales_settings(): void
    {
        $category = ExperienceCategory::query()->create([
            'name' => 'Wellness',
            'slug' => 'wellness',
            'is_active' => true,
        ]);
        $experience = Experience::query()->create([
            'experience_category_id' => $category->id,
            'title' => 'River Ritual',
            'slug' => 'river-ritual',
            'description' => '<p>Original wording.</p>',
            'is_active' => true,
        ]);
        ExperiencePrice::query()->create([
            'experience_id' => $experience->id,
            'price' => 2000000,
            'currency' => 'IDR',
            'price_type' => 'net',
            'unit_type' => 'per_person',
            'is_active' => true,
        ]);

        $synchronizer = app(ExperienceVoucherSynchronizer::class);
        $synchronizer->synchronize();

        $voucher = Voucher::query()->where('experience_id', $experience->id)->firstOrFail();
        $voucher->update(['discount_percentage' => 20, 'validity_days' => 90, 'is_featured' => true]);

        $experience->update([
            'title' => 'Sacred River Ritual',
            'slug' => 'sacred-river-ritual',
            'description' => '<p>Updated wording.</p>',
        ]);
        $experience->prices()->firstOrFail()->update(['price' => 2500000]);

        $synchronizer->synchronize();

        $voucher->refresh();
        $this->assertSame('Sacred River Ritual', $voucher->title);
        $this->assertSame('sacred-river-ritual', $voucher->slug);
        $this->assertSame('<p>Updated wording.</p>', $voucher->description);
        $this->assertSame(2500000, $voucher->selling_price);
        $this->assertSame(20, $voucher->discount_percentage);
        $this->assertSame(90, $voucher->validity_days);
        $this->assertTrue($voucher->is_featured);
        $this->assertSame(1, Voucher::query()->where('experience_id', $experience->id)->count());
    }
}
