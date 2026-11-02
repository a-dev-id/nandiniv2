<?php

namespace Tests\Feature;

use App\Models\GuestReview;
use App\Models\Member;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionImage;
use App\Support\HomepageCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomepageCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        HomepageCache::forget();
    }

    public function test_anonymous_homepage_reuses_the_public_cms_cache(): void
    {
        $this->createHomepage();

        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cached homepage introduction.');

        $coldQueryCount = count(DB::getQueryLog());

        DB::flushQueryLog();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cached homepage introduction.');

        $warmQueryCount = count(DB::getQueryLog());

        $this->assertTrue(Cache::has(HomepageCache::KEY));
        $this->assertGreaterThan($warmQueryCount, $coldQueryCount);
        $this->assertGreaterThanOrEqual(10, $coldQueryCount - $warmQueryCount);
    }

    public function test_authenticated_navigation_stays_outside_the_public_cache(): void
    {
        $this->createHomepage();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Welcome! Maya');

        $member = Member::query()->create([
            'first_name' => 'Maya',
            'last_name' => 'Guest',
            'email' => 'maya@example.test',
            'password' => 'secret-password',
        ]);

        $this->actingAs($member, 'member')
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Welcome! Maya')
            ->assertSee('Cached homepage introduction.');

        $cachedContent = serialize(Cache::get(HomepageCache::KEY));

        $this->assertStringNotContainsString('maya@example.test', $cachedContent);
        $this->assertStringNotContainsString('Maya Guest', $cachedContent);
    }

    public function test_public_payload_is_compatible_with_the_database_cache_store(): void
    {
        Cache::setDefaultDriver('database');
        HomepageCache::forget();
        $this->createHomepage();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cached homepage introduction.');

        $cachedPayload = Cache::get(HomepageCache::KEY);

        $this->assertIsArray($cachedPayload);
        $this->assertIsArray($cachedPayload['page']);
        $this->assertIsArray($cachedPayload['offers']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cached homepage introduction.');
    }

    public function test_cms_and_media_changes_invalidate_the_homepage_cache(): void
    {
        $page = $this->createHomepage();
        $section = PageSection::query()->create([
            'page_id' => $page->id,
            'section_key' => 'contained_image_section',
            'title' => 'Cached media section',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $image = PageSectionImage::query()->create([
            'page_section_id' => $section->id,
            'image' => 'pages/home/cache-test.webp',
            'image_alt' => 'Original cached image alt',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Original cached image alt');

        $page->update(['subtitle' => 'Updated homepage subtitle']);

        $this->assertFalse(Cache::has(HomepageCache::KEY));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Updated homepage subtitle');

        $review = GuestReview::query()->create([
            'reviewer_name' => 'New CMS Guest',
            'review_text' => '<p>Created after the cache was warmed.</p>',
            'excerpt' => 'Created after the cache was warmed.',
            'rating' => 5,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->assertFalse(Cache::has(HomepageCache::KEY));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('New CMS Guest');

        $image->update(['image_alt' => 'Updated cached image alt']);

        $this->assertFalse(Cache::has(HomepageCache::KEY));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Updated cached image alt')
            ->assertDontSee('Original cached image alt');

        $review->delete();

        $this->assertFalse(Cache::has(HomepageCache::KEY));

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('New CMS Guest');
    }

    public function test_public_content_cache_expires_after_five_minutes(): void
    {
        $page = $this->createHomepage();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Initial homepage subtitle');

        DB::table('pages')
            ->where('id', $page->id)
            ->update(['subtitle' => 'Subtitle loaded after cache expiry']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Initial homepage subtitle')
            ->assertDontSee('Subtitle loaded after cache expiry');

        $this->travel(HomepageCache::TTL_SECONDS + 1)->seconds();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Subtitle loaded after cache expiry');
    }

    private function createHomepage(): Page
    {
        return Page::query()->forceCreate([
            'id' => 1,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Home Page',
            'title' => 'Cached Homepage',
            'slug' => 'home',
            'subtitle' => 'Initial homepage subtitle',
            'description' => '<p>Cached homepage introduction.</p>',
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }
}
