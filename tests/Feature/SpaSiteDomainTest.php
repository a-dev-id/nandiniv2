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
            ->assertSee('Wellness at Nandini Jungle')
            ->assertSee('Essence Spa')
            ->assertSee('Wellness in the Heart of Nature')
            ->assertSee('Book a Spa Experience')
            ->assertSee('Explore Treatments')
            ->assertSeeInOrder([
                'Opening Hours',
                'Location',
                'Advance Booking',
                'Reservations',
                '+62 812 3687 1170',
                'Our Philosophy',
                'A Deeper Sense',
                'Signature Treatments',
                'Journeys of Renewal',
                '2-Day Balinese Wellness Escape',
                '3-Day Inner Harmony Retreat',
                '4-Day Deep Balinese Wellness Immersion',
                'A Unique Setting',
                'Spa on the River',
                'Why Nandini Jungle Spa',
                'Wellness Rooted in Nature',
                'Natural Surroundings',
                'Authentic Balinese Rituals',
                'Personalised Care',
                'River-Side Tranquillity',
                'Guest Experience',
                'Your Wellness Journey Awaits',
                'Book Your Spa Experience',
            ])
            ->assertDontSee('(WhatsApp)')
            ->assertDontSee('Learn More')
            ->assertSee('href="https://wa.me/6281236871170"', false)
            ->assertSee('md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]', false)
            ->assertSee('aspect-[4/3]', false)
            ->assertSee('spa-wellness-journey-image aspect-4/3', false)
            ->assertSee('order-1', false)
            ->assertSee('id="mainNavbar"', false)
            ->assertSee('Copyright ©')
            ->assertDontSee('aria-label="Spa navigation"', false)
            ->assertDontSee('Visit the main Nandini website')
            ->assertSee('aria-label="Chat with us on WhatsApp"', false)
            ->assertSee('nandini-mini-popup-closed-date', false)
            ->assertSee('spa-wellness-journeys-carousel', false)
            ->assertSee('data-slides-to-show="3"', false)
            ->assertSee('aria-label="Previous wellness journey"', false)
            ->assertSee('aspect-[4/3]', false)
            ->assertSee('https://'.config('domains.main'), false);
    }

    public function test_spa_homepage_renders_and_hides_the_new_cms_sections(): void
    {
        SpaSetting::query()->firstOrFail()->update([
            'signature_eyebrow' => 'CMS signature eyebrow',
            'signature_heading' => 'CMS signature heading',
            'signature_description' => 'CMS signature description.',
            'signature_image' => 'spa/signature-experience/test.webp',
            'signature_image_alt' => 'CMS signature image',
            'signature_link_label' => 'CMS signature link',
            'signature_link_url' => '/signature',
            'guest_review_quote' => 'CMS guest review quote.',
            'guest_review_label' => 'CMS guest label',
            'guest_review_image' => 'spa/guest-review/test.webp',
            'guest_review_image_alt' => 'CMS guest review image',
            'booking_cta_eyebrow' => 'CMS booking eyebrow',
            'booking_cta_heading' => 'CMS booking heading',
            'booking_cta_description' => 'CMS booking description.',
            'booking_cta_button_label' => 'CMS booking button',
            'booking_cta_button_url' => 'https://wa.me/123',
            'booking_cta_image' => 'spa/booking-cta/test.webp',
            'booking_cta_image_alt' => 'CMS booking background',
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSeeInOrder([
                'CMS signature heading',
                'CMS guest review quote.',
                'CMS booking heading',
            ])
            ->assertSee('spa/signature-experience/test.webp')
            ->assertSee('CMS signature image')
            ->assertSee('href="/signature"', false)
            ->assertSee('spa/guest-review/test.webp')
            ->assertSee('CMS guest review image')
            ->assertSee('spa/booking-cta/test.webp')
            ->assertSee('CMS booking background')
            ->assertSee('href="https://wa.me/123"', false);

        SpaSetting::query()->firstOrFail()->update([
            'signature_visible' => false,
            'guest_review_visible' => false,
            'booking_cta_visible' => false,
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertDontSee('CMS signature heading')
            ->assertDontSee('CMS guest review quote.')
            ->assertDontSee('CMS booking heading');
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

    public function test_spa_homepage_renders_cms_why_nandini_content_in_order(): void
    {
        SpaSetting::query()->firstOrFail()->update([
            'why_nandini_eyebrow' => 'CMS why eyebrow',
            'why_nandini_heading' => 'CMS why heading',
            'why_nandini_items' => [
                ['icon' => 'river', 'title' => 'First CMS benefit', 'description' => 'First CMS description.'],
                ['icon' => 'care', 'title' => 'Second CMS benefit', 'description' => 'Second CMS description.'],
                ['icon' => 'ritual', 'title' => 'Third CMS benefit', 'description' => 'Third CMS description.'],
                ['icon' => 'jungle', 'title' => 'Fourth CMS benefit', 'description' => 'Fourth CMS description.'],
            ],
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSee('CMS why eyebrow')
            ->assertSee('CMS why heading')
            ->assertSeeInOrder([
                'First CMS benefit',
                'Second CMS benefit',
                'Third CMS benefit',
                'Fourth CMS benefit',
            ])
            ->assertSee('lg:grid-cols-4', false)
            ->assertSee('min-[420px]:grid-cols-2', false);
    }

    public function test_spa_homepage_renders_cms_wellness_journeys_in_order(): void
    {
        SpaSetting::query()->firstOrFail()->update([
            'wellness_journeys_eyebrow' => 'CMS journeys eyebrow',
            'wellness_journeys_heading' => 'CMS journeys heading',
            'wellness_journeys_description' => 'CMS journeys description.',
            'wellness_journeys_items' => [
                [
                    'title' => 'First CMS journey',
                    'description' => 'First journey description.',
                    'image' => 'spa/wellness-journeys/first.webp',
                    'image_alt' => 'First image',
                    'details_label' => 'First details',
                    'details_url' => '/spa-wellness/first',
                    'book_label' => 'First booking',
                    'book_url' => 'https://wa.me/111',
                ],
                [
                    'title' => 'Second CMS journey',
                    'description' => 'Second journey description.',
                    'image' => 'spa/wellness-journeys/second.webp',
                    'image_alt' => 'Second image',
                    'details_label' => 'Second details',
                    'details_url' => '/spa-wellness/second',
                    'book_label' => 'Second booking',
                    'book_url' => 'https://wa.me/222',
                ],
            ],
        ]);

        $this->get('https://'.config('domains.spa').'/')
            ->assertOk()
            ->assertSee('CMS journeys eyebrow')
            ->assertSee('CMS journeys heading')
            ->assertSee('CMS journeys description.')
            ->assertSeeInOrder(['First CMS journey', 'Second CMS journey'])
            ->assertSee('https://'.config('domains.main').'/spa-wellness/first', false)
            ->assertSee('https://wa.me/222', false);
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
