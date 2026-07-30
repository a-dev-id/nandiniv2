<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VoucherSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_voucher_share_metadata_uses_the_voucher_image(): void
    {
        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
        ]);

        $voucher = Voucher::factory()->create([
            'title' => 'River Spa Voucher',
            'card_image' => 'vouchers/river-spa-card.webp',
            'image' => 'vouchers/river-spa-main.webp',
        ]);

        $this->get('http://voucher.nandinibali.test/voucher/'.$voucher->slug)
            ->assertOk()
            ->assertSee('property="og:image"', false)
            ->assertSee('/storage/vouchers/river-spa-card.webp', false)
            ->assertSee('name="twitter:image"', false);
    }

    public function test_voucher_page_displays_the_full_description(): void
    {
        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
        ]);

        $voucher = Voucher::factory()->create([
            'excerpt' => 'Short voucher summary.',
            'description' => '<p>The complete voucher description from the product catalogue.</p>',
        ]);

        $this->get('http://voucher.nandinibali.test/voucher/'.$voucher->slug)
            ->assertOk()
            ->assertSee('The complete voucher description from the product catalogue.')
            ->assertSee('<p>The complete voucher description from the product catalogue.</p>', false);
    }

    public function test_vouchers_are_sorted_by_the_displayed_discounted_price(): void
    {
        $regular = Voucher::factory()->create([
            'title' => 'Regular Experience',
            'selling_price' => 2000000,
            'discount_percentage' => 0,
        ]);
        $discounted = Voucher::factory()->create([
            'title' => 'Discounted Experience',
            'selling_price' => 3000000,
            'discount_percentage' => 50,
        ]);
        $cheapest = Voucher::factory()->create([
            'title' => 'Cheapest Experience',
            'selling_price' => 1000000,
            'discount_percentage' => 0,
        ]);

        $this->assertSame(
            [$cheapest->id, $discounted->id, $regular->id],
            Voucher::query()->cheapestFirst()->pluck('id')->all(),
        );
    }

    public function test_most_popular_slider_uses_the_first_seven_featured_vouchers_by_sort_order(): void
    {
        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
        ]);

        foreach (range(1, 8) as $position) {
            Voucher::factory()->create([
                'title' => 'Featured Voucher '.$position,
                'is_featured' => true,
                'sort_order' => $position,
            ]);
        }

        $response = $this->get('http://voucher.nandinibali.test/')->assertOk();
        $featuredSection = Str::before($response->getContent(), 'All Experiences');

        foreach (range(1, 7) as $position) {
            $this->assertStringContainsString('Featured Voucher '.$position, $featuredSection);
        }

        $this->assertStringNotContainsString('Featured Voucher 8', $featuredSection);
    }
}
