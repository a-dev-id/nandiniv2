<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeddingSeoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_wedding_page_has_commercial_content_metadata_schema_tracking_and_inquiry_flow(): void
    {
        $page = Page::query()->forceCreate([
            'id' => 8,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Wedding Page',
            'slug' => 'wedding',
            'title' => 'Jungle Wedding Venue in Ubud, Bali',
            'subtitle' => "Celebrate Your Story Surrounded by Bali's Jungle",
            'description' => '<p>Set within the tropical landscape of Payangan, in the greater Ubud area, Nandini Jungle by Hanging Gardens offers an intimate destination wedding setting surrounded by rainforest and the Ayung River valley.</p>',
            'hero_image' => 'pages/hero/wedding.webp',
            'hero_mobile_image' => 'pages/hero-mobile/wedding.webp',
            'hero_image_alt' => 'Destination wedding in the tropical jungle at Nandini Bali',
            'hero_mobile_image_alt' => 'Destination wedding in the tropical jungle at Nandini Bali',
            'meta_title' => 'Jungle Wedding Venue in Ubud, Bali | Nandini Jungle',
            'meta_description' => 'Celebrate your wedding at Nandini Jungle, a jungle wedding venue in Ubud, Bali with a private chapel, Ayung River ceremony setting, dining and accommodation.',
            'is_active' => true,
        ]);

        $chapel = $this->createSection($page, 59, 2, 'wedding_chapel', 'JUNGLE WEDDING CHAPEL', 'WEDDING BY THE CHAPEL', '<p>An intimate chapel environment framed by the jungle.</p>');
        $river = $this->createSection($page, 60, 3, 'wedding_river', 'AYUNG RIVER WEDDING', 'WEDDING BY THE RIVER', '<p>A ceremony setting beside the Ayung River.</p>');
        $ceremonies = $this->createSection($page, 61, 4, 'wedding_ceremony_options', 'CEREMONY EXPERIENCES', 'YOUR CEREMONY, YOUR STORY', '<p>Choose a ceremony direction.</p>', [
            ['title' => 'BALINESE-INSPIRED CEREMONY', 'description' => 'Inspired by Balinese tradition.'],
            ['title' => 'CLASSIC WESTERN CEREMONY', 'description' => 'A classic ceremony style.'],
            ['title' => 'SIGNATURE ENCHANTING WEDDING', 'description' => "Nandini's signature concept."],
        ]);
        $dining = $this->createSection($page, 62, 5, 'wedding_dining', 'DINING & CELEBRATION', 'WEDDING DINING & CELEBRATIONS', '<p>A custom wedding menu created for the occasion.</p>', null, 'EXPLORE DINING', 'https://dining.nandinibali.com/');
        $accommodation = $this->createSection($page, 63, 6, 'wedding_accommodation', 'DESTINATION WEDDING STAY', 'STAY TOGETHER IN THE JUNGLE', '<p>Extend the celebration with a <a href="https://nandinibali.com/honeymoon">honeymoon</a>.</p>', [
            ['title' => 'JUNGLE VILLAS', 'description' => 'Private jungle villas.', 'url' => 'https://nandinibali.com/jungle-villas', 'link_label' => 'EXPLORE JUNGLE VILLAS'],
            ['title' => 'ROYAL SUITES', 'description' => 'Spacious Royal Suites.', 'url' => 'https://nandinibali.com/the-royal-suites', 'link_label' => 'EXPLORE ROYAL SUITES'],
        ]);
        $planning = $this->createSection($page, 64, 7, 'wedding_planning', 'WEDDING PLANNING', 'PLANNING YOUR WEDDING AT NANDINI', '<p>Explore the arrangements available at the resort.</p>', [
            ['title' => 'VENUE', 'description' => 'Choose the chapel or river.'],
            ['title' => 'CEREMONY', 'description' => 'Select a supported ceremony style.'],
            ['title' => 'DINING', 'description' => 'Discuss a custom menu.'],
            ['title' => 'STAY', 'description' => 'Explore villas and suites.'],
        ]);
        $finalCta = $this->createSection($page, 65, 8, 'wedding_final_cta', 'YOUR CELEBRATION BEGINS HERE', 'BEGIN YOUR WEDDING JOURNEY', '<p>Tell us how you imagine your celebration.</p>', [
            ['label' => 'PLAN YOUR WEDDING', 'url' => '#wedding-inquiry', 'style' => 'solid'],
            ['label' => 'WHATSAPP OUR TEAM', 'url' => 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20planning%20a%20wedding%20at%20Nandini%20Jungle%20by%20Hanging%20Gardens%20in%20Ubud%2C%20Bali.', 'style' => 'white-outline'],
        ]);

        $this->createImage($chapel, 'pages/sections/chapel.webp', 'Jungle wedding chapel at Nandini Jungle in Ubud');
        $this->createImage($river, 'pages/sections/river.webp', 'Wedding ceremony setting beside the Ayung River at Nandini Jungle');
        $this->createImage($dining, '/images/dining/romantic-dining-by-the-chapel.webp', 'Romantic candlelit dining by the chapel at Nandini Jungle');
        $this->createImage($finalCta, 'pages/hero/wedding.webp', 'Destination wedding in the tropical jungle at Nandini Bali');

        $response = $this->get('https://'.config('domains.main').'/weddings')->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('<title>Jungle Wedding Venue in Ubud, Bali | Nandini Jungle</title>', false)
            ->assertSee('<meta name="description" content="Celebrate your wedding at Nandini Jungle, a jungle wedding venue in Ubud, Bali with a private chapel, Ayung River ceremony setting, dining and accommodation.">', false)
            ->assertSee('<link rel="canonical" href="https://nandinibali.com/weddings">', false)
            ->assertSeeInOrder([
                'Jungle Wedding Venue in Ubud, Bali',
                'WEDDING BY THE CHAPEL',
                'WEDDING BY THE RIVER',
                'YOUR CEREMONY, YOUR STORY',
                'WEDDING DINING &amp; CELEBRATIONS',
                'STAY TOGETHER IN THE JUNGLE',
                'PLANNING YOUR WEDDING AT NANDINI',
                'BEGIN YOUR WEDDING JOURNEY',
            ], false)
            ->assertSee('data-gtm-section="hero"', false)
            ->assertSee('data-gtm-section="wedding_intro"', false)
            ->assertSee('data-gtm-section="wedding_chapel"', false)
            ->assertSee('data-gtm-section="wedding_river"', false)
            ->assertSee('data-gtm-section="ceremony_options"', false)
            ->assertSee('data-gtm-section="wedding_dining"', false)
            ->assertSee('data-gtm-section="wedding_accommodation"', false)
            ->assertSee('data-gtm-section="wedding_planning"', false)
            ->assertSee('data-gtm-section="booking_cta"', false)
            ->assertSee('data-inquiry-button', false)
            ->assertSee('data-inquiry-title="Wedding Planning at Nandini Jungle"', false)
            ->assertSee('action="/inquiries"', false)
            ->assertSee('https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20planning%20a%20wedding%20at%20Nandini%20Jungle%20by%20Hanging%20Gardens%20in%20Ubud%2C%20Bali.', false)
            ->assertSee('https://nandinibali.com/jungle-villas', false)
            ->assertSee('https://nandinibali.com/the-royal-suites', false)
            ->assertSee('https://dining.nandinibali.com/', false)
            ->assertSee('https://nandinibali.com/honeymoon', false)
            ->assertSee('"@type":"WebPage"', false)
            ->assertSee('https://nandinibali.com/weddings#webpage', false)
            ->assertSee('https://nandinibali.com/#website', false)
            ->assertSee('https://nandinibali.com/#hotel', false)
            ->assertDontSee('FAQPage')
            ->assertDontSee('aggregateRating')
            ->assertDontSee('CAPACITY');

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    private function createSection(
        Page $page,
        int $id,
        int $sortOrder,
        string $key,
        string $subtitle,
        string $title,
        string $description,
        ?array $items = null,
        ?string $buttonLabel = null,
        ?string $buttonUrl = null,
    ): PageSection {
        return PageSection::query()->forceCreate([
            'id' => $id,
            'page_id' => $page->id,
            'section_key' => $key,
            'subtitle' => $subtitle,
            'title' => $title,
            'description' => $description,
            'items' => $items,
            'button_label' => $buttonLabel,
            'button_link_type' => 'manual',
            'button_url' => $buttonUrl,
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);
    }

    private function createImage(PageSection $section, string $image, string $alt): void
    {
        PageSectionImage::query()->create([
            'page_section_id' => $section->id,
            'image' => $image,
            'image_alt' => $alt,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
