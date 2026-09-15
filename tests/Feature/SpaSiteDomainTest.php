<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\Spa;
use App\Models\SpaSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SpaSiteDomainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['domains.spa_enabled' => true]);
    }

    public function test_main_homepage_still_resolves_on_the_main_domain(): void
    {
        $this->createPage(['id' => 1, 'slug' => 'main-home']);

        $this->get('https://'.config('domains.main').'/')->assertOk();
    }

    public function test_spa_master_switch_blocks_homepage_and_generic_pages_but_not_main_spa_pages(): void
    {
        $this->createPage(['id' => 1, 'slug' => 'main-home']);
        $this->createPage(['id' => 6, 'slug' => 'spa-wellness']);
        $this->createPage(['site' => Page::SITE_SPA, 'slug' => 'home']);
        $this->createPage(['site' => Page::SITE_SPA, 'slug' => 'test-page']);

        config(['domains.spa_enabled' => false]);

        $this->get('https://'.config('domains.spa').'/')->assertNotFound();
        $this->get('https://'.config('domains.spa').'/test-page')->assertNotFound();
        $this->get('https://'.config('domains.main').'/')->assertOk();
        $this->get('https://'.config('domains.main').'/spa-wellness')->assertOk();

        config(['domains.spa_enabled' => true]);

        $this->get('https://'.config('domains.spa').'/')->assertOk();
        $this->get('https://'.config('domains.spa').'/test-page')->assertOk();
    }

    public function test_existing_main_spa_landing_page_still_resolves(): void
    {
        $this->createPage(['id' => 6, 'slug' => 'spa-wellness']);

        $this->get('https://'.config('domains.main').'/spa-wellness')->assertOk();
    }

    public function test_spa_homepage_resolves_an_active_spa_home_page(): void
    {
        $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'home',
            'title' => 'Spa Homepage',
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSee('WELLNESS AT NANDINI JUNGLE')
            ->assertSee('RESTORE IN THE HEART')
            ->assertSee('BOOK A SPA EXPERIENCE')
            ->assertSee('EXPLORE TREATMENTS')
            ->assertSeeInOrder([
                'Opening Hours',
                '08:00 AM – 10:00 PM',
                'Booking',
                'Advance booking recommended',
                'Location',
                'Nandini Jungle, Ubud, Bali',
                'Reservations',
                '+62 812 3687 1170',
                '(WhatsApp)',
            ])
            ->assertSee('href="https://wa.me/6281236871170"', false)
            ->assertSeeInOrder([
                '(WhatsApp)',
                'OUR WELLNESS PHILOSOPHY',
                'A SACRED PAUSE',
                'IN THE JUNGLE',
                'At Nandini Jungle, wellness is a harmonious journey of body, mind and spirit',
            ])
            ->assertSee('md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]', false)
            ->assertSee('aspect-[4/3]', false)
            ->assertSee('order-1', false)
            ->assertSee('id="mainNavbar"', false)
            ->assertSee('Copyright ©')
            ->assertDontSee('aria-label="Spa navigation"', false)
            ->assertDontSee('Visit the main Nandini website')
            ->assertSee('aria-label="Chat with us on WhatsApp"', false)
            ->assertSee('nandini-mini-popup-closed-date', false)
            ->assertSee('https://'.config('domains.main'), false);
    }

    public function test_spa_accent_scope_and_shared_navigation_do_not_leak_into_the_main_homepage(): void
    {
        $this->createPage(['id' => 1, 'slug' => 'main-home']);
        $this->createPage(['site' => Page::SITE_SPA, 'slug' => 'home']);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSee('id="spa-hero-title"', false)
            ->assertDontSee('class="spa-site"', false);

        $this->get('https://'.config('domains.main').'/')
            ->assertOk()
            ->assertDontSee('id="spa-hero-title"', false);
    }

    public function test_spa_homepage_renders_spa_landing_settings(): void
    {
        SpaSetting::query()->firstOrFail()->update([
            'hero_eyebrow' => 'CMS supplied spa eyebrow',
            'hero_heading' => "CMS supplied spa\nheading",
            'hero_description' => 'CMS supplied spa description',
            'hero_image' => 'pages/hero/spa-home.webp',
            'hero_image_alt' => 'CMS supplied spa hero alt text',
            'information_bar_items' => [
                ['icon' => 'clock', 'label' => 'Hours Test', 'value' => '09:00 AM – 09:00 PM'],
                ['icon' => 'calendar', 'label' => 'Booking Test', 'value' => 'Book ahead'],
                ['icon' => 'location', 'label' => 'Location Test', 'value' => 'Ubud, Bali'],
                ['icon' => 'phone', 'label' => 'Contact Test', 'value' => '+62 812 3687 1170', 'link' => 'https://wa.me/6281236871170'],
            ],
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSee('CMS supplied spa eyebrow')
            ->assertSee("CMS supplied spa<br />\nheading", false)
            ->assertSee('CMS supplied spa description')
            ->assertSee('pages/hero/spa-home.webp')
            ->assertSee('CMS supplied spa hero alt text')
            ->assertSeeInOrder(['Hours Test', 'Booking Test', 'Location Test', 'Contact Test'])
            ->assertSee('min-h-[80svh]', false)
            ->assertSee('href="https://wa.me/6281236871170"', false);
    }

    public function test_spa_homepage_renders_cms_wellness_philosophy_content(): void
    {
        SpaSetting::query()->firstOrFail()->update([
            'wellness_philosophy_eyebrow' => 'CMS philosophy eyebrow',
            'wellness_philosophy_heading' => "CMS philosophy\nheading",
            'wellness_philosophy_description' => 'CMS philosophy description.',
            'wellness_philosophy_image' => 'spa/wellness-philosophy/test.webp',
            'wellness_philosophy_image_alt' => 'CMS philosophy image alt text',
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSee('CMS philosophy eyebrow')
            ->assertSee("CMS philosophy<br />\nheading", false)
            ->assertSee('CMS philosophy description.')
            ->assertSee('spa/wellness-philosophy/test.webp')
            ->assertSee('CMS philosophy image alt text');
    }

    public function test_main_page_cannot_be_displayed_on_the_spa_domain(): void
    {
        $this->createPage(['slug' => 'main-only-page']);

        $this->get('https://'.config('domains.spa').'/main-only-page')->assertNotFound();
    }

    public function test_spa_page_cannot_be_displayed_on_the_main_domain(): void
    {
        $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'spa-only-page',
        ]);

        $this->get('https://'.config('domains.main').'/spa-only-page')
            ->assertRedirect('https://'.config('domains.main'));
    }

    public function test_inactive_spa_page_cannot_be_displayed(): void
    {
        $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'inactive-spa-page',
            'is_active' => false,
        ]);

        $this->get('https://'.config('domains.spa').'/inactive-spa-page')->assertNotFound();
    }

    public function test_spa_pages_do_not_leak_into_the_main_sitemap(): void
    {
        $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'spa-sitemap-page',
        ]);

        $this->get('https://'.config('domains.main').'/sitemap.xml')
            ->assertOk()
            ->assertDontSee('spa-sitemap-page');
    }

    public function test_spa_route_names_and_generated_domains_are_isolated(): void
    {
        $this->assertSame(config('domains.spa'), parse_url(route('spa-landing.index'), PHP_URL_HOST));
        $this->assertSame(config('domains.spa'), Route::getRoutes()->getByName('spa-landing.index')?->getDomain());
        $this->assertSame(config('domains.main'), Route::getRoutes()->getByName('home')?->getDomain());
    }

    public function test_generic_spa_page_renders_active_sections_in_sort_order(): void
    {
        $page = $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'wellness-page',
            'title' => 'Wellness Page',
        ]);

        $this->createSection($page, 'Second Active Section', true, 20);
        $this->createSection($page, 'Hidden Section', false, 5);
        $this->createSection($page, 'First Active Section', true, 10);

        $response = $this->get('https://'.config('domains.spa').'/wellness-page')
            ->assertOk()
            ->assertSee('Wellness Page')
            ->assertSeeInOrder(['First Active Section', 'Second Active Section'])
            ->assertDontSee('Hidden Section');
    }

    public function test_spa_page_uses_cms_seo_and_spa_canonical_url(): void
    {
        $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'seo-page',
            'title' => 'Fallback Title',
            'meta_title' => 'Spa SEO Title',
            'meta_description' => 'Spa SEO description.',
        ]);

        $this->get('https://'.config('domains.spa').'/seo-page')
            ->assertOk()
            ->assertSee('<title>Spa SEO Title</title>', false)
            ->assertSee('<meta name="description" content="Spa SEO description.">', false)
            ->assertSee('<link rel="canonical" href="https://'.config('domains.spa').'/seo-page">', false)
            ->assertSee('<meta property="og:url" content="https://'.config('domains.spa').'/seo-page">', false);
    }

    public function test_missing_mobile_hero_falls_back_without_broken_storage_urls(): void
    {
        $this->createPage([
            'site' => Page::SITE_SPA,
            'slug' => 'desktop-hero-page',
            'hero_image' => 'pages/hero/spa-desktop.webp',
            'hero_image_alt' => 'A valid CMS supplied description',
        ]);

        $this->get('https://'.config('domains.spa').'/desktop-hero-page')
            ->assertOk()
            ->assertSee('pages/hero/spa-desktop.webp')
            ->assertSee('A valid CMS supplied description')
            ->assertDontSee('src="/storage/"', false);
    }

    private function createPage(array $attributes = []): Page
    {
        return Page::unguarded(fn (): Page => Page::query()->create(array_merge([
            'site' => Page::SITE_MAIN,
            'page_name' => 'Test Page',
            'title' => 'Test Page',
            'slug' => 'test-page-'.uniqid(),
            'is_active' => true,
            'sort_order' => 0,
        ], $attributes)));
    }

    private function createSection(Page $page, string $title, bool $active, int $sortOrder): PageSection
    {
        return PageSection::query()->create([
            'page_id' => $page->id,
            'section_key' => 'intro_text_section',
            'title' => $title,
            'is_active' => $active,
            'sort_order' => $sortOrder,
        ]);
    }

    private function createSpa(string $title, int $sortOrder, array $attributes = []): Spa
    {
        return Spa::query()->create(array_merge([
            'title' => $title,
            'slug' => str($title)->slug(),
            'excerpt' => 'Published wellness package summary.',
            'is_active' => true,
            'sort_order' => $sortOrder,
        ], $attributes));
    }
}
