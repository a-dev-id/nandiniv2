<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AboutUsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_the_tailwind_story_layout_from_active_filament_sections(): void
    {
        $page = Page::query()->create([
            'site' => Page::SITE_MAIN,
            'page_name' => 'About Us Page',
            'title' => 'About Us',
            'slug' => 'about-us',
            'meta_title' => 'About Nandini',
            'meta_description' => 'The story of Nandini Jungle.',
            'is_active' => true,
        ]);

        $hero = $this->section($page, [
            'section_key' => 'about_story_hero',
            'title' => 'Rooted in the Jungle Since 2005',
            'subtitle' => 'Our Story',
            'excerpt' => 'Susut, Payangan · Bali',
            'description' => '<p>A story shaped by the jungle.</p>',
            'button_label' => 'Discover Our Story',
            'button_url' => '#our-story',
            'sort_order' => 1,
        ]);

        $hero->images()->create([
            'image' => 'https://nandinibali.com/storage/images/gallery/pool.jpg',
            'image_alt' => 'Nandini jungle pool',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->section($page, [
            'section_key' => 'about_story_timeline',
            'title' => 'A Story in Time',
            'subtitle' => 'Two Decades of Nandini',
            'items' => [[
                'year' => '2005',
                'title' => 'Nandini Opens',
                'description' => 'Nandini opens with 18 hillside villas.',
            ]],
            'sort_order' => 2,
        ]);

        $this->section($page, [
            'section_key' => 'about_story_values',
            'title' => 'Hidden Draft Section',
            'is_active' => false,
            'sort_order' => 3,
        ]);

        $this->section($page, [
            'section_key' => 'about_story_final',
            'title' => 'Be Part of Our Continuing Story',
            'items' => [
                ['label' => 'Plan Your Stay', 'url' => 'https://example.com/book'],
                ['label' => 'Explore Nandini', 'url' => '/explore'],
            ],
            'sort_order' => 4,
        ]);

        $response = $this->get('http://nandinibali.test/about-us');

        $response->assertOk()
            ->assertSee('<title>About Nandini</title>', false)
            ->assertSee('Rooted in the Jungle Since 2005')
            ->assertSee('A story shaped by the jungle.', false)
            ->assertSee('https://nandinibali.com/storage/images/gallery/pool.jpg', false)
            ->assertSee('Nandini jungle pool')
            ->assertSee('A Story in Time')
            ->assertSee('Nandini Opens')
            ->assertSee('border-slate-300', false)
            ->assertSee('text-xl font-medium uppercase text-slate-700 sm:text-2xl', false)
            ->assertSee('bg-[#A88444]', false)
            ->assertSee('Plan Your Stay')
            ->assertDontSee('Explore Nandini')
            ->assertDontSee('Hidden Draft Section');
    }

    public function test_about_page_falls_back_to_the_existing_page_content_when_story_sections_are_absent(): void
    {
        Page::query()->create([
            'site' => Page::SITE_MAIN,
            'page_name' => 'About Us Page',
            'title' => 'About Us',
            'slug' => 'about-us',
            'description' => '<p>Existing about page content.</p>',
            'hero_image' => 'pages/hero/about.webp',
            'is_active' => true,
        ]);

        $this->get('http://nandinibali.test/about-us')
            ->assertOk()
            ->assertSee('Existing about page content.', false)
            ->assertSee('pages/hero/about.webp', false)
            ->assertDontSee('Rooted in the Jungle Since 2005');
    }

    public function test_administrator_can_edit_an_about_story_section_with_a_numeric_year(): void
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $page = Page::query()->create([
            'site' => Page::SITE_MAIN,
            'page_name' => 'About Us Page',
            'title' => 'About Us',
            'slug' => 'about-us',
            'is_active' => true,
        ]);

        $section = $this->section($page, [
            'section_key' => 'about_story_chapter',
            'title' => 'The Beginning',
            'excerpt' => '2005',
        ]);

        Livewire::test(SectionsRelationManager::class, [
            'ownerRecord' => $page,
            'pageClass' => EditPage::class,
        ])
            ->assertOk()
            ->mountTableAction('edit', $section)
            ->assertOk()
            ->assertTableActionDataSet(['excerpt' => '2005']);
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
}
