<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Models\Accommodation;
use App\Models\Honeymoon;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HoneymoonLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_honeymoon_page_uses_the_new_landing_layout_with_shared_navigation_and_footer(): void
    {
        Page::query()->forceCreate([
            'id' => 7,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Honeymoon Page',
            'title' => 'Honeymoon Bali Packages',
            'slug' => 'honeymoon',
            'hero_image' => 'pages/hero/honeymoon.webp',
            'hero_mobile_image' => 'pages/hero-mobile/honeymoon.webp',
            'meta_title' => 'Honeymoon Resort in Ubud, Bali | Nandini Jungle',
            'meta_description' => 'Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Ubud, Bali with private villas, spa, dining and couples experiences.',
            'is_active' => true,
        ]);

        Honeymoon::query()->create([
            'title' => 'Honeymoon Packages - 4 Days 3 Nights',
            'slug' => 'honeymoon-packages-4-days-3-nights',
            'booking_url_override' => 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance',
            'is_featured' => true,
            'is_active' => true,
        ]);

        foreach ([
            ['Panoramic Corner Jacuzzi Royal Suite', 'panoramic-corner-jacuzzi-royal-suite', 'suite', 30],
            ['Private Garden Royal Suite', 'private-garden-royal-suite', 'suite', 20],
            ['Panoramic Jungle View Villa', 'panoramic-jungle-view-villa', 'villa', 10],
        ] as [$title, $slug, $type, $sortOrder]) {
            Accommodation::query()->create([
                'title' => $title,
                'slug' => $slug,
                'card_image' => "accommodations/cards/{$slug}.webp",
                'accommodation_type' => $type,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]);
        }

        $response = $this->get('http://nandinibali.test/honeymoon');

        $response
            ->assertOk()
            ->assertViewIs('pages.honeymoon.index')
            ->assertSee('<nav id="mainNavbar"', false)
            ->assertSee('<footer class="bg-black text-white"', false)
            ->assertSee('<title>Honeymoon Resort in Ubud, Bali | Nandini Jungle</title>', false)
            ->assertSee('<meta name="description" content="Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Ubud, Bali with private villas, spa, dining and couples experiences.">', false)
            ->assertSee('<link rel="canonical" href="https://nandinibali.com/honeymoon">', false)
            ->assertSee('Honeymoon Resort in Ubud, Bali')
            ->assertSee('Celebrate Your Honeymoon Surrounded by the Rainforest')
            ->assertSeeInOrder([
                'Panoramic Jungle View Villa',
                'Private Garden Royal Suite',
                'Panoramic Corner Jacuzzi Royal Suite',
            ])
            ->assertSee('/honeymoon/honeymoon-packages-4-days-3-nights', false)
            ->assertSee('https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&amp;bkcode=romance', false)
            ->assertDontSee('checkin=2026-05-27', false)
            ->assertSee('Explore Our Resort')
            ->assertSee('Explore Villas &amp; Royal Suites', false)
            ->assertSee('View Details')
            ->assertSee('Explore Spa &amp; Wellness', false)
            ->assertSee('Plan a Romantic Celebration')
            ->assertSee('Reserve Your Stay')
            ->assertSee('Honeymoon in Ubud — Frequently Asked Questions')
            ->assertSee('application/ld+json', false)
            ->assertSee('Honeymoon experience at Nandini Jungle by Hanging Gardens in Ubud, Bali')
            ->assertSee('4 Days 3 Nights honeymoon package at Nandini Jungle by Hanging Gardens')
            ->assertSee('Romantic jungle dining experience for couples at Nandini Jungle by Hanging Gardens')
            ->assertSee('Couples spa and wellness experience at Nandini Jungle by Hanging Gardens')
            ->assertSee('Romantic proposal and anniversary celebration at Nandini Jungle by Hanging Gardens')
            ->assertSee('Romantic honeymoon escape at Nandini Jungle by Hanging Gardens in Ubud')
            ->assertSee('Whether you are planning a Bali honeymoon, anniversary or romantic escape')
            ->assertSee('Couples can stay in private jungle accommodation')
            ->assertSee('The Panoramic Jungle View Villa is ideal for couples who want a deeper sense of privacy and connection with nature.')
            ->assertDontSee('&rarr;', false)
            ->assertDontSee('→');

        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertSame(1, substr_count($html, '<script type="application/ld+json">'));
        $this->assertStringNotContainsString('<?php', $this->faqJson($html));
        $this->assertStringNotContainsString('$__contextArgs', $this->faqJson($html));
        $this->assertStringNotContainsString('context()->', $this->faqJson($html));

        $schema = json_decode($this->faqJson($html), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame('FAQPage', $schema['@type']);
    }

    public function test_honeymoon_landing_content_is_rendered_from_filament_page_sections(): void
    {
        $page = Page::query()->forceCreate([
            'id' => 7,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Honeymoon Page',
            'title' => 'Honeymoon Bali Packages',
            'slug' => 'honeymoon-bali-packages',
            'hero_image' => 'pages/hero/honeymoon.webp',
            'is_active' => true,
        ]);

        $hero = $this->section($page, [
            'section_key' => 'honeymoon_hero',
            'subtitle' => 'Editable Hero Eyebrow',
            'title' => 'Editable Honeymoon Hero',
            'description' => '<p>Editable hero subtitle.</p>',
            'sort_order' => 10,
        ]);
        $hero->images()->create([
            'image' => 'pages/sections/editable-hero.webp',
            'image_alt' => 'Editable hero image',
            'is_active' => true,
        ]);

        $this->section($page, [
            'section_key' => 'honeymoon_intro',
            'subtitle' => 'Editable Introduction Eyebrow',
            'title' => 'Editable Introduction Heading',
            'description' => '<p>Editable introduction copy.</p>',
            'button_label' => 'Editable Introduction Button',
            'button_url' => '#why',
            'sort_order' => 15,
        ]);

        $this->section($page, [
            'section_key' => 'honeymoon_features',
            'subtitle' => 'Editable Features Eyebrow',
            'title' => 'Editable Features Heading',
            'description' => '<p>Editable features introduction.</p>',
            'items' => [[
                'icon' => 'heart',
                'title' => 'Editable Feature Card',
                'description' => 'Editable feature card copy.',
            ]],
            'sort_order' => 20,
        ]);

        $this->section($page, [
            'section_key' => 'honeymoon_faq',
            'subtitle' => 'Editable FAQ Eyebrow',
            'title' => 'Editable FAQ Heading',
            'items' => [[
                'question' => 'Can this question be edited?',
                'answer' => 'Yes, directly from the Filament page section.',
            ]],
            'sort_order' => 30,
        ]);

        $response = $this->get('http://nandinibali.test/honeymoon');

        $response
            ->assertOk()
            ->assertSee('pages/sections/editable-hero.webp', false)
            ->assertSee('Editable Hero Eyebrow')
            ->assertSee('Editable Honeymoon Hero')
            ->assertSee('Editable hero subtitle.')
            ->assertSee('Editable Introduction Eyebrow')
            ->assertSee('Editable Introduction Heading')
            ->assertSee('Editable introduction copy.', false)
            ->assertSee('Editable Introduction Button')
            ->assertSee('Editable Features Heading')
            ->assertSee('Editable Feature Card')
            ->assertSee('Editable feature card copy.')
            ->assertSee('Editable FAQ Heading')
            ->assertSee('Can this question be edited?')
            ->assertSee('Yes, directly from the Filament page section.')
            ->assertDontSee('A Suggested 4-Day Honeymoon in Ubud');

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
    }

    public function test_administrator_can_edit_honeymoon_section_content_in_filament(): void
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $page = Page::query()->forceCreate([
            'id' => 7,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Honeymoon Page',
            'title' => 'Honeymoon Bali Packages',
            'slug' => 'honeymoon-bali-packages',
            'is_active' => true,
        ]);

        $section = $this->section($page, [
            'section_key' => 'honeymoon_features',
            'subtitle' => 'Why Choose Nandini',
            'title' => 'Editable Features',
            'items' => [[
                'icon' => 'heart',
                'title' => 'Romantic Dining',
                'description' => 'An intimate experience for two.',
            ]],
        ]);

        Livewire::test(SectionsRelationManager::class, [
            'ownerRecord' => $page,
            'pageClass' => EditPage::class,
        ])
            ->assertOk()
            ->mountTableAction('edit', $section)
            ->assertOk()
            ->assertTableActionDataSet([
                'section_key' => 'honeymoon_features',
                'title' => 'Editable Features',
            ]);
    }

    private function section(Page $page, array $attributes): PageSection
    {
        return $page->sections()->create(array_merge([
            'title' => null,
            'subtitle' => null,
            'description' => null,
            'button_link_type' => 'manual',
            'is_active' => true,
            'sort_order' => 0,
        ], $attributes));
    }

    private function faqJson(string $html): string
    {
        preg_match('/<script type="application\/ld\+json">\s*(.*?)\s*<\/script>/s', $html, $matches);

        return $matches[1] ?? '';
    }
}
