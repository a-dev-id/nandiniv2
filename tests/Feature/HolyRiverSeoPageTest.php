<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolyRiverSeoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_holy_river_page_has_focused_metadata_content_and_tracking(): void
    {
        PageSection::query()->whereIn('id', [8, 9, 10, 11, 12])->delete();

        $page = Page::query()->forceCreate([
            'id' => 24,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Holy River Page',
            'slug' => 'holy-river',
            'title' => 'Sacred Holy River & Balinese Purification in Ubud',
            'subtitle' => 'A SPIRITUAL JOURNEY BY THE AYUNG RIVER',
            'excerpt' => "Along the Ayung River below Nandini Jungle by Hanging Gardens, guests can discover a quieter side of Balinese spirituality through sacred water, traditional blessings and moments of reflection surrounded by the jungle.\n\nThe Holy River experience is centred on Balinese purification traditions, including Melukat, a ritual associated with spiritual cleansing and renewal.",
            'meta_title' => 'Balinese Purification & Holy River in Ubud | Nandini Jungle',
            'meta_description' => 'Experience a Balinese purification ritual by the sacred Ayung River at Nandini Jungle in Ubud, with Melukat, traditional blessings and riverside experiences.',
            'is_active' => true,
        ]);

        $this->createSection($page, 8, 1, 'image_overlay_section', 'BALINESE PURIFICATION', 'THE MEANING OF MELUKAT', '<p>Melukat is a Balinese purification ritual associated with cleansing, renewal and spiritual reflection beside the Ayung River.</p>');
        $this->createSection($page, 9, 2, 'split_media_reverse', 'THE AYUNG RIVER', 'A SACRED SETTING BY THE AYUNG RIVER', '<p>A peaceful jungle setting for reflection and Balinese rituals.</p>');
        $this->createSection($page, 11, 3, 'intro_text_section', 'SACRED RITUALS BY THE RIVER', 'HOLY RIVER EXPERIENCES', '<p>Discover purification and blessing experiences beside the river.</p>');
        $this->createSection($page, 10, 4, 'split_media_section', 'BALINESE TRADITION', 'BALINESE BLESSING & PURIFICATION', '<p>A blessing and purification experience led by a Pemangku.</p>');
        $this->createSection($page, 12, 5, 'image_overlay_section', 'RIVERSIDE WELLNESS', 'SPA ON THE RIVER', '<p>A short complementary riverside spa experience.</p>', 'EXPLORE ESSENCE SPA', 'https://spa.nandinibali.com/');

        $category = ExperienceCategory::query()->create([
            'name' => 'Holy River',
            'slug' => 'holy-river',
            'is_active' => true,
        ]);

        $this->createExperience($category->id, 'Sacred Waters: Half-Day Ubud Healing Retreat', 'sacred-waters-half-day-ubud-healing-retreat', 1);
        $this->createExperience($category->id, 'Balinese Blessing Purification at the Holy River', 'balinese-blessing-purification-at-the-holy-river', 2);
        $this->createExperience($category->id, 'Nandini Signature: Spa on the River', 'nandini-signature-spa-on-the-river', 3);

        $response = $this->get('https://'.config('domains.main').'/holy-river')->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('<title>Balinese Purification &amp; Holy River in Ubud | Nandini Jungle</title>', false)
            ->assertSee('<meta name="description" content="Experience a Balinese purification ritual by the sacred Ayung River at Nandini Jungle in Ubud, with Melukat, traditional blessings and riverside experiences.">', false)
            ->assertSee('<link rel="canonical" href="https://nandinibali.com/holy-river">', false)
            ->assertSee('Sacred Holy River &amp; Balinese Purification in Ubud', false)
            ->assertSeeInOrder([
                'THE MEANING OF MELUKAT',
                'A SACRED SETTING BY THE AYUNG RIVER',
                'HOLY RIVER EXPERIENCES',
                'BALINESE BLESSING &amp; PURIFICATION',
                'SPA ON THE RIVER',
                'PLAN YOUR HOLY RIVER EXPERIENCE',
            ], false)
            ->assertSee('data-gtm-section="holy_river_intro"', false)
            ->assertSee('data-gtm-section="melukat"', false)
            ->assertSee('data-gtm-section="ayung_river"', false)
            ->assertSee('id="holy-river-experiences"', false)
            ->assertSee('data-gtm-section="holy_river_experiences_intro"', false)
            ->assertSee('data-gtm-section="holy_river_experiences"', false)
            ->assertSee('data-gtm-section="blessing_purification"', false)
            ->assertSee('data-gtm-section="spa_on_river"', false)
            ->assertSee('data-gtm-section="booking_cta"', false)
            ->assertSee('/images/holy-river/A-SACRED-SETTING-BY-THE-AYUNG-RIVER.jpg', false)
            ->assertSee('/images/holy-river/BALINESE-BLESSING-&amp;-PURIFICATION.jpg', false)
            ->assertSee('/images/holy-river/SPA%20ON%20THE%20RIVER.webp', false)
            ->assertSee('/images/holy-river/PLAN%20YOUR%20HOLY%20RIVER%20EXPERIENCE.webp', false)
            ->assertSee('href="#holy-river-experiences"', false)
            ->assertSee('EXPLORE HOLY RIVER EXPERIENCES')
            ->assertSee('RESERVE')
            ->assertDontSee('ENQUIRE / RESERVE')
            ->assertSee('https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20the%20Holy%20River%20and%20Balinese%20purification%20experiences%20at%20Nandini%20Jungle.', false)
            ->assertSee('/holy-river/sacred-waters-half-day-ubud-healing-retreat', false)
            ->assertSee('/holy-river/balinese-blessing-purification-at-the-holy-river', false)
            ->assertDontSee('/holy-river/nandini-signature-spa-on-the-river', false)
            ->assertSee('https://spa.nandinibali.com/', false)
            ->assertSee('"@type":"WebPage"', false)
            ->assertSee('https://nandinibali.com/holy-river#webpage', false)
            ->assertSee('https://nandinibali.com/#website', false)
            ->assertSee('https://nandinibali.com/#hotel', false)
            ->assertDontSee('potent spiritual energy')
            ->assertDontSee('Recharge in Bali')
            ->assertDontSee('Luxury Amidst Untouched Nature');

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(1, substr_count($html, 'id="holy-river-experiences"'));
    }

    private function createSection(
        Page $page,
        int $id,
        int $sortOrder,
        string $key,
        string $subtitle,
        string $title,
        string $description,
        ?string $buttonLabel = null,
        ?string $buttonUrl = null,
    ): void {
        PageSection::query()->forceCreate([
            'id' => $id,
            'page_id' => $page->id,
            'section_key' => $key,
            'subtitle' => $subtitle,
            'title' => $title,
            'description' => $description,
            'button_label' => $buttonLabel,
            'button_link_type' => 'manual',
            'button_url' => $buttonUrl,
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);
    }

    private function createExperience(int $categoryId, string $title, string $slug, int $sortOrder): void
    {
        Experience::query()->create([
            'experience_category_id' => $categoryId,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'A factual Holy River experience beside the Ayung River.',
            'is_active' => true,
            'sort_order' => $sortOrder,
        ]);
    }
}
