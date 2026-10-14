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

    public function test_jungle_spa_page_renders_refined_content_links_and_unchanged_hero(): void
    {
        $page = Page::query()
            ->where('site', Page::SITE_MAIN)
            ->where('slug', 'jungle-spa-ubud')
            ->firstOrFail();

        $this->assertSame('SEO - Jungle Spa Ubud Page', $page->page_name);
        $this->assertSame('Jungle Spa Ubud | Spa in the Bali Jungle | Nandini', $page->meta_title);
        $this->assertCount(12, $page->sections);
        $this->assertCount(7, $page->sections->where('is_active', true));
        $this->assertSame(
            'soft_gray',
            $page->sections->firstWhere('title', 'Plan Your Spa Experience')?->background_color,
        );

        $response = $this->get('https://'.config('domains.main').'/jungle-spa-ubud');

        $response
            ->assertOk()
            ->assertViewIs('pages.show')
            ->assertSee('<title>Jungle Spa Ubud | Spa in the Bali Jungle | Nandini</title>', false)
            ->assertSee('Jungle Spa in Ubud, Bali')
            ->assertSee('Discover Our Spa Experiences')
            ->assertSee('Essence Spa')
            ->assertSee('Nandini Signature Spa on the River')
            ->assertSee('Wine Spa')
            ->assertSee('Spa Jacuzzi')
            ->assertSee('A Romantic Spa Escape')
            ->assertSee('Plan Your Spa Experience')
            ->assertSee('Explore Essence Spa')
            ->assertSee('View Spa on the River')
            ->assertSee('View Wine Spa')
            ->assertSee('Book Spa Jacuzzi')
            ->assertSee('Explore Honeymoon Experiences')
            ->assertSee('Book a Spa Experience')
            ->assertSee('href="https://spa.nandinibali.com/"', false)
            ->assertSee('href="/holy-river/nandini-signature-spa-on-the-river"', false)
            ->assertSee('href="https://voucher.nandinibali.com/voucher/luxury-spa-package-wine-spa"', false)
            ->assertSee('href="/ubud-wellness-retreat"', false)
            ->assertSee('href="/honeymoon"', false)
            ->assertSee('href="https://'.config('domains.main').'/jungle-spa-ubud"', false)
            ->assertSee('alt="Balinese spa treatment at Essence Spa in Ubud, Bali"', false)
            ->assertSee('alt="Riverside spa treatment beside the Ayung River at Nandini Jungle"', false)
            ->assertDontSee('Frequently Asked Questions')
            ->assertDontSee('FAQPage', false)
            ->assertDontSee('A Spa Experience Shaped by the Jungle')
            ->assertDontSee('Why Experience a Jungle Spa at Nandini?');

        $html = $response->getContent();

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(1, preg_match('/<header\b[^>]*data-gtm-section="hero"[^>]*>(.*?)<\/header>/is', $html, $hero));
        $this->assertStringNotContainsString('<h1', $hero[1]);
        $this->assertStringNotContainsString('<a ', $hero[1]);
        $this->assertStringNotContainsString('<button', $hero[1]);
        $this->assertStringContainsString('loading="eager"', $hero[1]);
        $this->assertStringContainsString('fetchpriority="high"', $hero[1]);
        $this->assertSame(6, substr_count($html, 'data-image-aspect="4:3"'));
        $this->assertSame(7, substr_count($html, 'data-text-spacing="comfortable"'));

        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get(PageResource::getUrl('edit', ['record' => $page]))
            ->assertOk()
            ->assertSee('Jungle Spa in Ubud, Bali')
            ->assertSee('Include in Sitemap')
            ->assertSee('Sections');
    }
}
