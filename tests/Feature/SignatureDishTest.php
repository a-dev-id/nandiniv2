<?php

namespace Tests\Feature;

use App\Filament\Resources\SignatureDishes\SignatureDishResource;
use App\Models\Role;
use App\Models\SignatureDish;
use App\Models\SignatureDishSection;
use App\Models\SignatureDishSectionImage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignatureDishTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_uses_first_published_signature_dish_in_display_order(): void
    {
        SignatureDish::query()->delete();
        $later = $this->dish(['name' => 'Later Dish', 'slug' => 'later-dish', 'sort_order' => 2]);
        $first = $this->dish([
            'name' => 'First Dish',
            'slug' => 'first-dish',
            'cta_url' => '/signature-dishes/first-dish',
            'sort_order' => 1,
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee($first->name)
            ->assertDontSee($later->name)
            ->assertSee('aspect-[4/3]', false)
            ->assertSee('href="/signature-dishes/first-dish"', false);
    }

    public function test_signature_dish_section_uses_all_cms_fields_and_precedes_dish_of_the_month(): void
    {
        SignatureDish::query()->delete();
        $dish = $this->dish([
            'name' => 'CMS Signature Title',
            'eyebrow' => 'CMS Eyebrow',
            'subtitle' => 'CMS Subtitle',
            'short_description' => 'CMS description selected by the dining team.',
            'cta_label' => 'CMS CTA',
            'cta_url' => 'https://example.com/signature',
            'image_alt' => 'CMS-provided alternative text',
        ]);

        $response = $this->get('https://'.config('domains.dining').'/')->assertOk()
            ->assertSee($dish->name)
            ->assertSee('CMS Eyebrow')
            ->assertSee('CMS Subtitle')
            ->assertSee('CMS description selected by the dining team.')
            ->assertSee('href="https://example.com/signature"', false)
            ->assertSee('alt="CMS-provided alternative text"', false)
            ->assertSee('md:grid-cols-[minmax(0,45fr)_minmax(0,55fr)]', false)
            ->assertSee('order-1 aspect-[16/10]', false)
            ->assertSee('md:order-2', false);

        $html = $response->getContent();
        $this->assertLessThan(strpos($html, 'Dish of the Month'), strpos($html, 'CMS Signature Title'));
    }

    public function test_signature_dish_detail_uses_database_content_and_four_by_three_image(): void
    {
        SignatureDish::query()->delete();
        $dish = $this->dish([
            'name' => 'Database Dish',
            'slug' => 'database-dish',
            'price' => '$125',
            'short_description' => 'Database short description.',
            'content' => '<p>Database full content.</p>',
            'meta_title' => 'Database dish SEO title',
            'meta_description' => 'Database dish SEO description.',
        ]);

        $this->get('https://'.config('domains.dining').'/signature-dishes/'.$dish->slug)
            ->assertOk()
            ->assertSee('Database Dish')
            ->assertSee('$125')
            ->assertSee('Database short description.')
            ->assertSee('<p>Database full content.</p>', false)
            ->assertSee('<title>Database dish SEO title</title>', false)
            ->assertSee('aspect-[4/3]', false);
    }

    public function test_signature_dish_detail_renders_active_ordered_content_sections(): void
    {
        SignatureDish::query()->delete();
        $dish = $this->dish(['name' => 'Sectioned Dish', 'slug' => 'sectioned-dish']);
        $second = SignatureDishSection::query()->create([
            'signature_dish_id' => $dish->id,
            'section_key' => 'intro_text_section',
            'title' => 'Second content section',
            'description' => '<p>Rich second section content.</p>',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $first = SignatureDishSection::query()->create([
            'signature_dish_id' => $dish->id,
            'section_key' => 'split_media_section',
            'title' => 'First content section',
            'description' => '<p>Rich first section content.</p>',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        SignatureDishSectionImage::query()->create([
            'signature_dish_section_id' => $first->id,
            'image' => '/images/dining/rahang-tuna.jpeg',
            'image_alt' => 'Section image alt text',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        SignatureDishSection::query()->create([
            'signature_dish_id' => $dish->id,
            'section_key' => 'intro_text_section',
            'title' => 'Hidden content section',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $response = $this->get('https://'.config('domains.dining').'/signature-dishes/'.$dish->slug)
            ->assertOk()
            ->assertSee($first->title)
            ->assertSee($second->title)
            ->assertSee('<p>Rich first section content.</p>', false)
            ->assertSee('alt="Section image alt text"', false)
            ->assertDontSee('Hidden content section');

        $this->assertLessThan(
            strpos($response->getContent(), $second->title),
            strpos($response->getContent(), $first->title),
        );
    }

    public function test_unpublished_signature_dish_is_hidden_and_returns_not_found(): void
    {
        SignatureDish::query()->delete();
        $dish = $this->dish(['name' => 'Hidden Dish', 'slug' => 'hidden-dish', 'is_published' => false]);

        $this->get('https://'.config('domains.dining').'/')->assertOk()->assertDontSee('Hidden Dish');
        $this->get('https://'.config('domains.dining').'/signature-dishes/'.$dish->slug)->assertNotFound();
    }

    public function test_administrator_can_open_signature_dish_filament_resource(): void
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get(SignatureDishResource::getUrl('index'))->assertOk()->assertSee('Signature Dish');
        $this->get(SignatureDishResource::getUrl('create'))->assertOk()
            ->assertSee('Main Image')->assertSee('Full Content')->assertSee('Subtitle')->assertSee('CTA URL');

        $dish = $this->dish(['slug' => 'filament-content-sections']);
        $this->get(SignatureDishResource::getUrl('edit', ['record' => $dish]))
            ->assertOk();
        $this->assertContains(
            \App\Filament\Resources\SignatureDishes\RelationManagers\SectionsRelationManager::class,
            SignatureDishResource::getRelations(),
        );
    }

    private function dish(array $overrides = []): SignatureDish
    {
        return SignatureDish::query()->create(array_merge([
            'name' => 'Signature Dish',
            'slug' => 'signature-dish',
            'eyebrow' => 'Signature Dish',
            'subtitle' => 'A Signature Taste of Bali',
            'price' => '$100',
            'short_description' => 'Short description.',
            'cta_label' => 'More Details',
            'cta_url' => '/signature-dishes/signature-dish',
            'image' => '/images/dining/rahang-tuna.jpeg',
            'image_alt' => 'Signature dish image',
            'content' => '<p>Full content.</p>',
            'is_published' => true,
            'sort_order' => 1,
        ], $overrides));
    }
}
