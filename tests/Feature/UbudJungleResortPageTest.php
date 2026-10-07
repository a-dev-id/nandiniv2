<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UbudJungleResortPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders_current_cms_content_and_metadata_in_database_order(): void
    {
        $page = Page::query()->create([
            'site' => Page::SITE_MAIN,
            'page_name' => 'Ubud Jungle Resort Page',
            'title' => 'Luxury Jungle Resort in Ubud, Bali',
            'slug' => 'ubud-jungle-resort-in-bali',
            'subtitle' => 'Nandini Jungle by Hanging Gardens',
            'description' => '<p>Database-managed introduction.</p>',
            'hero_image' => 'pages/hero/jungle-resort.webp',
            'hero_image_alt' => 'Nandini jungle resort in Ubud',
            'meta_title' => 'Luxury Jungle Resort in Ubud, Bali | Nandini Jungle',
            'meta_description' => 'Database-managed SEO description.',
            'is_active' => true,
        ]);

        $this->section($page, [
            'section_key' => 'seo_split_media_section',
            'title' => 'Private Jungle Villas & Royal Suites',
            'description' => '<p>Villa content from the database.</p>',
            'button_label' => 'Explore Jungle View Villas',
            'button_url' => '/jungle-villas/jungle-view-villa',
            'sort_order' => 20,
        ]);

        $this->section($page, [
            'section_key' => 'seo_split_media_section',
            'title' => 'What Makes Nandini a Unique Jungle Resort in Ubud',
            'description' => '<p>Resort content from the database.</p>',
            'sort_order' => 10,
        ]);

        $this->section($page, [
            'section_key' => 'seo_split_media_reverse',
            'title' => 'Spa, Wellness & Riverside Experiences',
            'description' => '<p>Wellness content from the database.</p>',
            'button_label' => 'Discover Spa & Wellness',
            'button_url' => '/spa-wellness',
            'sort_order' => 30,
        ]);

        $this->section($page, [
            'section_key' => 'intro_text_section',
            'title' => 'Nandini Jungle Resort at a Glance',
            'description' => '<p>At-a-glance content from the database.</p>',
            'sort_order' => 40,
        ]);

        $this->section($page, [
            'section_key' => 'intro_text_section',
            'title' => 'Plan Your Jungle Stay in Ubud',
            'description' => '<p>Planning content from the database.</p>',
            'sort_order' => 50,
        ]);

        $this->section($page, [
            'section_key' => 'intro_text_section',
            'title' => 'Frequently Asked Questions',
            'description' => '<h3>Where is Nandini located?</h3><p>Nandini is in Payangan in the greater Ubud area.</p>',
            'sort_order' => 60,
        ]);

        $this->section($page, [
            'section_key' => 'intro_text_section',
            'title' => 'Exclusive Direct Booking Benefits',
            'description' => '<p>Inactive legacy content.</p>',
            'is_active' => false,
            'sort_order' => 70,
        ]);

        $response = $this->get('http://nandinibali.test/ubud-jungle-resort-in-bali');

        $response
            ->assertOk()
            ->assertViewIs('pages.show')
            ->assertSee('<title>Luxury Jungle Resort in Ubud, Bali | Nandini Jungle</title>', false)
            ->assertSee('<meta name="description" content="Database-managed SEO description.">', false)
            ->assertSee('<link rel="canonical" href="http://nandinibali.test/ubud-jungle-resort-in-bali">', false)
            ->assertSee('Luxury Jungle Resort in Ubud, Bali')
            ->assertSee('Nandini Jungle by Hanging Gardens')
            ->assertSeeInOrder([
                'What Makes Nandini a Unique Jungle Resort in Ubud',
                'Private Jungle Villas &amp; Royal Suites',
                'Spa, Wellness &amp; Riverside Experiences',
                'Nandini Jungle Resort at a Glance',
                'Plan Your Jungle Stay in Ubud',
                'Frequently Asked Questions',
            ], false)
            ->assertSee('<h3>Where is Nandini located?</h3>', false)
            ->assertSee('<p>Nandini is in Payangan in the greater Ubud area.</p>', false)
            ->assertSee('href="/jungle-villas/jungle-view-villa"', false)
            ->assertSee('Explore Jungle View Villas')
            ->assertSee('href="/spa-wellness"', false)
            ->assertSee('Discover Spa &amp; Wellness', false)
            ->assertDontSee('More Details')
            ->assertDontSee('application/ld+json', false)
            ->assertDontSee('Best Bali Jungle Resort in Ubud | Luxury Nature Escape')
            ->assertDontSee('Luxury Bali Jungle Resort in Ubud')
            ->assertDontSee('Why Choose a Jungle Resort in Ubud?')
            ->assertDontSee('Private Villas Surrounded by Tropical Jungle')
            ->assertDontSee('Wellness and Spa Experiences in Nature')
            ->assertDontSee('Why Travelers Choose Nandini')
            ->assertDontSee('Exclusive Direct Booking Benefits');

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
        $this->assertSame(1, substr_count($response->getContent(), 'Frequently Asked Questions'));
    }

    public function test_other_generic_pages_keep_the_existing_detail_button_behavior(): void
    {
        $page = Page::query()->create([
            'site' => Page::SITE_MAIN,
            'page_name' => 'Another SEO Page',
            'title' => 'Another SEO Page',
            'slug' => 'another-seo-page',
            'meta_title' => 'Another SEO Page',
            'meta_description' => 'Another SEO page description.',
            'is_active' => true,
        ]);

        $this->section($page, [
            'section_key' => 'seo_split_media_section',
            'title' => 'Another Section',
            'description' => '<p>Another database-managed section.</p>',
            'button_label' => 'Discover',
            'button_url' => '/jungle-villas/jungle-view-villa',
        ]);

        $this->get('http://nandinibali.test/another-seo-page')
            ->assertOk()
            ->assertSee('More Details')
            ->assertDontSee('>Discover</a>', false);
    }

    private function section(Page $page, array $attributes): PageSection
    {
        return $page->sections()->create(array_merge([
            'title' => null,
            'subtitle' => null,
            'description' => null,
            'button_link_type' => 'manual',
            'button_url' => null,
            'is_active' => true,
            'sort_order' => 0,
        ], $attributes));
    }
}
