<?php

namespace Tests\Feature;

use App\Models\BlogNews;
use App\Models\BlogNewsSection;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AyungRiverGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_ayung_river_guide_has_editorial_content_metadata_schema_links_and_tracking(): void
    {
        Page::query()->forceCreate([
            'id' => 15,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Blog & News Page',
            'slug' => 'blog-news',
            'title' => 'Blog & News',
            'is_active' => true,
        ]);

        $blog = BlogNews::query()->forceCreate([
            'id' => 33,
            'type' => 'blog',
            'title' => 'Ayung River in Ubud, Bali: Rafting, Nature & River Experiences',
            'slug' => 'ayung-river-ubud-guide',
            'description' => '<p>The Ayung River is one of Bali\'s major river systems and a defining natural feature of the Ubud and Payangan landscape.</p>',
            'author_name' => config('resort.name'),
            'published_at' => '2026-07-08',
            'meta_title' => 'Ayung River Ubud Guide: Rafting & River Experiences | Nandini',
            'meta_description' => 'Discover the Ayung River in Ubud, Bali, from white-water rafting and jungle scenery to Balinese purification, Holy River and riverside experiences.',
            'is_active' => true,
        ]);

        $this->createSection($blog, 1, 'AYUNG RIVER AT A GLANCE', 'QUICK FACTS ABOUT THE AYUNG RIVER', 'ayung_facts', [
            'presentation' => 'facts',
            'entries' => [
                ['label' => 'APPROX. 75 KM', 'description' => 'Official Indonesian tourism information describes the Ayung as stretching for about 75 kilometres.'],
            ],
            'source' => [
                'label' => 'Indonesia Travel, Ministry of Tourism of the Republic of Indonesia',
                'url' => 'https://www.indonesia.travel/gb/en/travel-ideas/marine/5-reasons-travelers-love-white-water-rafting-at-ayung-river-in-ubud',
            ],
        ]);
        $this->createSection($blog, 2, 'LOCATION', 'WHERE IS THE AYUNG RIVER IN BALI?', 'ayung_location');
        $this->createSection($blog, 3, 'LANDSCAPE & EXPERIENCE', 'WHAT MAKES THE AYUNG RIVER SPECIAL?', 'ayung_overview');
        $this->createSection($blog, 4, 'WHITE-WATER ADVENTURE', 'AYUNG RIVER RAFTING IN UBUD', 'ayung_rafting', [
            'preserve_button_label' => true,
        ], 'EXPLORE AYUNG RIVER RAFTING', 'https://nandinibali.com/experience/ayung-white-water-rafting');
        $this->createSection($blog, 5, 'BALINESE PURIFICATION', 'AYUNG RIVER FOR MELUKAT & BALINESE PURIFICATION', 'holy_river', [
            'preserve_button_label' => true,
        ], 'EXPLORE THE HOLY RIVER', 'https://nandinibali.com/holy-river');
        $this->createSection($blog, 6, 'TWO WAYS TO EXPERIENCE THE RIVER', 'RAFTING VS HOLY RIVER: WHICH EXPERIENCE FITS?', 'rafting_vs_holy_river');
        $this->createSection($blog, 7, 'RIVERSIDE WELLNESS', 'SPA ON THE RIVER', 'spa_on_river', [
            'preserve_button_label' => true,
        ], 'EXPLORE ESSENCE SPA', 'https://spa.nandinibali.com/');
        $this->createSection($blog, 8, 'RIVER CONDITIONS', 'BEST TIME TO EXPERIENCE THE AYUNG RIVER', 'river_conditions');
        $this->createSection($blog, 9, 'PLAN YOUR VISIT', 'PLANNING AN AYUNG RIVER EXPERIENCE', 'planning');
        $this->createSection($blog, 10, 'THE AYUNG RIVER AT NANDINI', 'EXPERIENCE THE AYUNG RIVER AT NANDINI', 'article_cta', [
            'presentation' => 'links',
            'entries' => [
                ['label' => 'STAY AT NANDINI', 'url' => 'https://nandinibali.com/ubud-jungle-resort-in-bali'],
            ],
        ]);

        $response = $this->get('https://'.config('domains.main').'/blog-news/ayung-river-ubud-guide')->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('<title>Ayung River Ubud Guide: Rafting &amp; River Experiences | Nandini</title>', false)
            ->assertSee('<meta name="description" content="Discover the Ayung River in Ubud, Bali, from white-water rafting and jungle scenery to Balinese purification, Holy River and riverside experiences.">', false)
            ->assertSee('<link rel="canonical" href="https://nandinibali.com/blog-news/ayung-river-ubud-guide">', false)
            ->assertSee('Ayung River in Ubud, Bali: Rafting, Nature &amp; River Experiences', false)
            ->assertSeeInOrder([
                'QUICK FACTS ABOUT THE AYUNG RIVER',
                'WHERE IS THE AYUNG RIVER IN BALI?',
                'WHAT MAKES THE AYUNG RIVER SPECIAL?',
                'AYUNG RIVER RAFTING IN UBUD',
                'AYUNG RIVER FOR MELUKAT &amp; BALINESE PURIFICATION',
                'RAFTING VS HOLY RIVER: WHICH EXPERIENCE FITS?',
                'SPA ON THE RIVER',
                'BEST TIME TO EXPERIENCE THE AYUNG RIVER',
                'PLANNING AN AYUNG RIVER EXPERIENCE',
                'EXPERIENCE THE AYUNG RIVER AT NANDINI',
            ], false)
            ->assertSee('APPROX. 75 KM')
            ->assertSee('Indonesia Travel, Ministry of Tourism of the Republic of Indonesia')
            ->assertSee('href="https://www.indonesia.travel/gb/en/travel-ideas/marine/5-reasons-travelers-love-white-water-rafting-at-ayung-river-in-ubud" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('EXPLORE AYUNG RIVER RAFTING')
            ->assertSee('https://nandinibali.com/experience/ayung-white-water-rafting', false)
            ->assertSee('EXPLORE THE HOLY RIVER')
            ->assertSee('https://nandinibali.com/holy-river', false)
            ->assertSee('EXPLORE ESSENCE SPA')
            ->assertSee('https://spa.nandinibali.com/', false)
            ->assertSee('https://nandinibali.com/ubud-jungle-resort-in-bali', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('https://nandinibali.com/blog-news/ayung-river-ubud-guide#article', false)
            ->assertSee('"about":{"@type":"Place","name":"Ayung River","containedInPlace":{"@type":"AdministrativeArea","name":"Bali, Indonesia"}}', false)
            ->assertSee('"datePublished":"2026-07-08"', false)
            ->assertSee('https://nandinibali.com/#organization', false)
            ->assertSee('https://chat.openai.com/?q=Visit%20this%20URL%20and%20summarize%20this%20article%20for%20me%3A%0Ahttps%3A%2F%2Fnandinibali.com%2Fblog-news%2Fayung-river-ubud-guide', false)
            ->assertSee('data-gtm-section="article_intro"', false)
            ->assertSee('data-gtm-section="ayung_facts"', false)
            ->assertSee('data-gtm-section="ayung_location"', false)
            ->assertSee('data-gtm-section="ayung_overview"', false)
            ->assertSee('data-gtm-section="ayung_rafting"', false)
            ->assertSee('data-gtm-section="holy_river"', false)
            ->assertSee('data-gtm-section="rafting_vs_holy_river"', false)
            ->assertSee('data-gtm-section="spa_on_river"', false)
            ->assertSee('data-gtm-section="river_conditions"', false)
            ->assertSee('data-gtm-section="planning"', false)
            ->assertSee('data-gtm-section="article_cta"', false)
            ->assertDontSee('FAQPage', false)
            ->assertDontSee('keep the domain', false)
            ->assertDontSee('future citations', false)
            ->assertDontSee('remember this domain', false);

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(1, substr_count($html, '"@type":"BlogPosting"'));
        $this->assertSame(0, preg_match_all('/<h[23][^>]*>\s*FAQ\s*<\/h[23]>/i', $html));
    }

    private function createSection(
        BlogNews $blog,
        int $sortOrder,
        string $subtitle,
        string $title,
        string $gtmSection,
        array $items = [],
        ?string $buttonLabel = null,
        ?string $buttonUrl = null,
    ): void {
        BlogNewsSection::query()->create([
            'blog_news_id' => $blog->id,
            'section_key' => 'intro_text_section',
            'title' => $title,
            'subtitle' => $subtitle,
            'description' => '<p>Clear editorial guidance about the Ayung River.</p>',
            'button_label' => $buttonLabel,
            'button_link_type' => 'manual',
            'button_url' => $buttonUrl,
            'text_align' => 'left',
            'background_color' => 'white',
            'items' => array_merge(['gtm_section' => $gtmSection], $items),
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);
    }
}
