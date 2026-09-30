<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JungleSpaSeoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_jungle_spa_seo_page_renders_attached_content_and_footer_link(): void
    {
        $page = Page::query()
            ->where('site', Page::SITE_MAIN)
            ->where('slug', 'jungle-spa-ubud')
            ->firstOrFail();

        $this->assertSame('SEO - Jungle Spa Ubud Page', $page->page_name);
        $this->assertSame('Jungle Spa Ubud | Spa in the Bali Jungle | Nandini', $page->meta_title);
        $this->assertCount(12, $page->sections);
        $this->assertSame(
            'soft_gray',
            $page->sections->firstWhere('title', 'Find Your Moment of Calm in the Bali Jungle')?->background_color,
        );

        $response = $this->get('https://'.config('domains.main').'/jungle-spa-ubud');

        $response
            ->assertOk()
            ->assertViewIs('pages.show')
            ->assertSee('<title>Jungle Spa Ubud | Spa in the Bali Jungle | Nandini</title>', false)
            ->assertSee('Jungle Spa in Ubud, Bali')
            ->assertSee('A Spa Experience Shaped by the Jungle')
            ->assertSee('Signature Spa on the River')
            ->assertSee('Why Experience a Jungle Spa at Nandini?')
            ->assertSee('Frequently Asked Questions')
            ->assertSee('Book Your Jungle Spa Experience')
            ->assertSee('/holy-river/nandini-signature-spa-on-the-river', false)
            ->assertSee('/storage/pages/sections/ubud-jungle-spa-treatment-nandini-wellness-retreat.webp', false)
            ->assertSee('href="https://'.config('domains.main').'/jungle-spa-ubud"', false)
            ->assertSee('Jungle Spa Ubud');

        $this->assertSame(7, substr_count($response->getContent(), 'data-image-aspect="4:3"'));
        $this->assertSame(12, substr_count($response->getContent(), 'data-text-spacing="comfortable"'));

        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get(PageResource::getUrl('edit', ['record' => $page]))
            ->assertOk()
            ->assertSee('Jungle Spa in Ubud, Bali')
            ->assertSee('Sections');
    }
}
