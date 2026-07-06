<?php

namespace Tests\Feature;

use App\Models\BlogNews;
use App\Models\Offer;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_includes_public_static_pages_and_published_content(): void
    {
        URL::forceRootUrl('https://nandinibali.com');
        URL::forceScheme('https');

        Page::create([
            'title' => 'Private Jungle Dining',
            'slug' => 'private-jungle-dining',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Page::create([
            'title' => 'Hidden Page',
            'slug' => 'hidden-page',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        Offer::create([
            'title' => 'Summer Escape',
            'slug' => 'summer-escape',
            'is_active' => true,
            'valid_start_date' => now()->subDay(),
            'valid_end_date' => now()->addDay(),
            'sort_order' => 1,
        ]);

        Offer::create([
            'title' => 'Expired Escape',
            'slug' => 'expired-escape',
            'is_active' => true,
            'valid_start_date' => now()->subDays(10),
            'valid_end_date' => now()->subDay(),
            'sort_order' => 2,
        ]);

        BlogNews::create([
            'type' => 'blog',
            'title' => 'A Day in the Jungle',
            'slug' => 'a-day-in-the-jungle',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 1,
        ]);

        BlogNews::create([
            'type' => 'blog',
            'title' => 'Future Story',
            'slug' => 'future-story',
            'is_active' => true,
            'published_at' => now()->addDay(),
            'sort_order' => 2,
        ]);

        $response = $this->get('/sitemap.xml');

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');

        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
        $response->assertSee('https://nandinibali.com/', false);
        $response->assertSee('https://nandinibali.com/private-jungle-dining', false);
        $response->assertSee('https://nandinibali.com/offer/summer-escape', false);
        $response->assertSee('https://nandinibali.com/blog-news/a-day-in-the-jungle', false);

        $response->assertDontSee('hidden-page', false);
        $response->assertDontSee('expired-escape', false);
        $response->assertDontSee('future-story', false);
    }
}
