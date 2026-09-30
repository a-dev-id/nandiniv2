<?php

namespace Tests\Feature;

use App\Models\DiningExperience;
use App\Models\Page;
use App\Models\SignatureDish;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_dining_sitemap_contains_only_published_dining_content(): void
    {
        DiningExperience::query()->create([
            'title' => 'Private Jungle Dinner',
            'slug' => 'private-jungle-dinner',
            'is_active' => true,
        ]);

        DiningExperience::query()->create([
            'title' => 'Hidden Dining Experience',
            'slug' => 'hidden-dining-experience',
            'is_active' => false,
        ]);

        SignatureDish::query()->create([
            'name' => 'Seasonal Balinese Menu',
            'slug' => 'seasonal-balinese-menu',
            'is_published' => true,
        ]);

        $baseUrl = 'https://'.config('domains.dining');

        $this->get($baseUrl.'/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>'.$baseUrl.'</loc>', false)
            ->assertSee($baseUrl.'/experiences/private-jungle-dinner', false)
            ->assertSee($baseUrl.'/signature-dishes/seasonal-balinese-menu', false)
            ->assertDontSee('hidden-dining-experience', false)
            ->assertDontSee('https://'.config('domains.main').'/', false);
    }

    public function test_spa_sitemap_contains_the_homepage_and_enabled_public_pages(): void
    {
        config(['domains.spa_enabled' => true]);

        Page::query()->create([
            'site' => Page::SITE_SPA,
            'page_name' => 'Spa Rituals',
            'title' => 'Spa Rituals',
            'slug' => 'spa-rituals',
            'is_active' => true,
            'include_in_sitemap' => true,
        ]);

        Page::query()->create([
            'site' => Page::SITE_SPA,
            'page_name' => 'Private Spa Page',
            'title' => 'Private Spa Page',
            'slug' => 'private-spa-page',
            'is_active' => true,
            'include_in_sitemap' => false,
        ]);

        $baseUrl = 'https://'.config('domains.spa');

        $this->get($baseUrl.'/sitemap.xml')
            ->assertOk()
            ->assertSee('<loc>'.$baseUrl.'</loc>', false)
            ->assertSee($baseUrl.'/spa-rituals', false)
            ->assertDontSee('private-spa-page', false);
    }

    public function test_voucher_sitemap_contains_public_categories_and_purchasable_vouchers(): void
    {
        config(['features.disable_voucher_feature' => false]);

        $category = VoucherCategory::factory()->create([
            'slug' => 'wellness-gifts',
            'is_active' => true,
        ]);

        Voucher::factory()->for($category, 'category')->create([
            'slug' => 'balinese-spa-gift',
            'selling_price' => 1_000_000,
            'is_active' => true,
        ]);

        Voucher::factory()->for($category, 'category')->create([
            'slug' => 'unavailable-gift',
            'selling_price' => 0,
            'is_active' => true,
        ]);

        $baseUrl = 'https://'.config('domains.voucher');

        $this->get($baseUrl.'/sitemap.xml')
            ->assertOk()
            ->assertSee('<loc>'.$baseUrl.'</loc>', false)
            ->assertSee($baseUrl.'/category/wellness-gifts', false)
            ->assertSee($baseUrl.'/voucher/balinese-spa-gift', false)
            ->assertDontSee('unavailable-gift', false);

        $this->get($baseUrl.'/category/wellness-gifts')->assertOk();
    }

    public function test_affiliate_sitemap_contains_only_the_public_landing_page(): void
    {
        config(['features.disable_affiliate_feature' => false]);

        $baseUrl = 'https://'.config('domains.affiliate');

        $this->get($baseUrl.'/sitemap.xml')
            ->assertOk()
            ->assertSee('<loc>'.$baseUrl.'</loc>', false)
            ->assertDontSee($baseUrl.'/login', false)
            ->assertDontSee($baseUrl.'/register', false);
    }

    public function test_each_public_domain_robots_file_points_to_its_own_sitemap(): void
    {
        config([
            'domains.spa_enabled' => true,
            'features.disable_voucher_feature' => false,
            'features.disable_affiliate_feature' => false,
        ]);

        foreach ([
            config('domains.main'),
            config('domains.dining'),
            config('domains.spa'),
            config('domains.voucher'),
            config('domains.affiliate'),
        ] as $host) {
            $this->get("https://{$host}/robots.txt")
                ->assertOk()
                ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->assertSee("Sitemap: https://{$host}/sitemap.xml", false);
        }
    }
}
