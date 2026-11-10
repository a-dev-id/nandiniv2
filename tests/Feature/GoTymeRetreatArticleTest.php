<?php

namespace Tests\Feature;

use App\Models\BlogNews;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class GoTymeRetreatArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Page::query()->forceCreate([
            'id' => 15, 'site' => Page::SITE_MAIN, 'page_name' => 'Blog & News',
            'slug' => 'blog-news', 'title' => 'Blog & News', 'is_active' => true,
        ]);
    }

    public function test_article_has_local_photography_seo_and_canonical_discovery(): void
    {
        $blog = BlogNews::where('slug', BlogNews::GO_TYME_SLUG)->firstOrFail();
        $response = $this->get($blog->publicUrl())->assertOk();
        $html = $response->getContent();
        $response->assertSee('<title>Go Tyme Group Retreat in Bali | Nandini Jungle</title>', false)
            ->assertSee('<link rel="canonical" href="https://nandinibali.com/blog/go-tyme-group-retreat-bali-2026">', false)
            ->assertSee('https://nandinibali.com/images/go-tyme-images/optimized/08615-1600.webp', false)
            ->assertSee('data-retreat-lightbox', false)
            ->assertSee('data-gtm-section="hero"', false)
            ->assertSee('data-gtm-section="article_intro"', false)
            ->assertSee('Share this article')
            ->assertDontSee('The Nandini Journal · Group Retreats')
            ->assertDontSee('Go Tyme Indonesia')
            ->assertDontSee('noindex');
        $this->assertSame(1, preg_match_all('/<h1\b/', $html));
        $this->assertSame(90, preg_match_all('/data-retreat-photo\s/', $html));
        preg_match_all('/<a[^>]*data-retreat-photo\s[^>]*>(.*?)<\/a>/s', $html, $photos);
        $this->assertSame(89, substr_count(implode('', $photos[1]), 'loading="lazy"'));

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $match);
        $schema = json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('BlogPosting', $schema['@type']);
        $this->assertSame($blog->published_at->toDateString(), $schema['datePublished']);
        $this->assertNotSame('2026-10-04', $schema['datePublished']);

        $this->get(route('blog.index'))->assertOk()
            ->assertSee($blog->publicUrl(), false)
            ->assertSee('/images/go-tyme-images/optimized/08615-960.webp', false)
            ->assertDontSee('/storage//images/go-tyme-images');
        $this->get(route('blog.show', $blog->slug))->assertRedirect($blog->publicUrl())->assertStatus(301);
        $this->get(route('sitemap'))->assertOk()->assertSee($blog->publicUrl(), false);
    }

    public function test_drafts_and_scheduled_articles_stay_private_but_signed_previews_work(): void
    {
        $blog = BlogNews::where('slug', BlogNews::GO_TYME_SLUG)->firstOrFail();
        foreach ([['is_active' => false], ['is_active' => true, 'published_at' => today()->addDay()]] as $state) {
            $blog->update($state);
            $this->get($blog->publicUrl())->assertRedirect(route('blog.index'));
            $this->get(route('blog.index'))->assertDontSee($blog->title);
            $this->get(route('sitemap'))->assertDontSee($blog->publicUrl(), false);
        }
        $this->get(route('blog.preview', $blog->slug))->assertForbidden();
        $this->get(URL::signedRoute('blog.preview', $blog->slug))->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertSee($blog->title);
    }

    public function test_existing_articles_keep_their_template_and_related_article_links(): void
    {
        $blog = BlogNews::create([
            'type' => 'blog', 'title' => 'An Existing Journal Story',
            'slug' => 'existing-journal-story', 'description' => '<p>Existing editorial content.</p>',
            'is_active' => true,
        ]);
        $retreat = BlogNews::where('slug', BlogNews::GO_TYME_SLUG)->firstOrFail();
        $this->get($blog->publicUrl())->assertOk()
            ->assertSee('Existing editorial content.')
            ->assertSee('https://nandinibali.com/blog-news/existing-journal-story', false)
            ->assertSee($retreat->publicUrl(), false)
            ->assertDontSee('/storage//images/go-tyme-images');
    }

    public function test_all_selected_assets_exist_and_migration_preserves_editorial_changes(): void
    {
        $dimensions = require database_path('content/go-tyme-image-dimensions.php');
        $this->assertCount(90, $dimensions);
        foreach ($dimensions as $id => $photo) {
            $this->assertFileExists(public_path('images/go-tyme-images/'.$photo['file']));
            foreach ([480, 960] as $width) {
                $path = public_path('images/go-tyme-images/optimized/'.$id.'-'.$width.'.webp');
                $this->assertFileExists($path);
                $size = getimagesize($path);
                $this->assertEqualsWithDelta($photo['width'] / $photo['height'], $size[0] / $size[1], .01);
            }
        }
        $content = require database_path('content/go-tyme-retreat.php');
        foreach (array_merge([$content['hero']], array_column($content['sections'], 'featured')) as $id) {
            $this->assertFileExists(public_path('images/go-tyme-images/optimized/'.$id.'-1600.webp'));
        }
        $blog = BlogNews::where('slug', BlogNews::GO_TYME_SLUG)->firstOrFail();
        $blog->update(['is_active' => false, 'title' => 'Editorial review']);
        $migration = require database_path('migrations/2026_11_02_000001_create_go_tyme_retreat_article.php');
        $migration->up();
        $this->assertSame('Editorial review', $blog->fresh()->title);
        $this->assertFalse($blog->fresh()->is_active);
        $this->assertSame(89, $blog->sections()->withCount('images')->get()->sum('images_count'));
    }
}
