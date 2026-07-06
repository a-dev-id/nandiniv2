<?php

namespace Tests\Feature;

use App\Models\BlogNews;
use App\Services\BlogNewsPublicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BlogNewsPublicationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_blog_news_active_state_from_published_date(): void
    {
        Carbon::setTestNow('2026-07-06 09:00:00');

        $futurePost = $this->createBlogNews('future-post', [
            'is_active' => true,
            'published_at' => '2026-07-10',
        ]);

        $publishingToday = $this->createBlogNews('publishing-today', [
            'is_active' => false,
            'published_at' => '2026-07-06',
        ]);

        $publishedPost = $this->createBlogNews('published-post', [
            'is_active' => true,
            'published_at' => '2026-07-01',
        ]);

        $manualDraftWithoutPublishDate = $this->createBlogNews('manual-draft', [
            'is_active' => false,
            'published_at' => null,
        ]);

        $summary = app(BlogNewsPublicationService::class)->sync();

        $this->assertSame([
            'activated' => 1,
            'deactivated_scheduled' => 1,
        ], $summary);

        $this->assertFalse($futurePost->fresh()->is_active);
        $this->assertTrue($publishingToday->fresh()->is_active);
        $this->assertTrue($publishedPost->fresh()->is_active);
        $this->assertFalse($manualDraftWithoutPublishDate->fresh()->is_active);
    }

    public function test_cron_endpoint_runs_sync_when_token_is_valid(): void
    {
        Carbon::setTestNow('2026-07-06 09:00:00');
        config(['services.blog_news.publication_cron_token' => 'test-blog-news-token']);

        $this->createBlogNews('publishing-today', [
            'is_active' => false,
            'published_at' => '2026-07-06',
        ]);

        $this->get('/cron/blog-news/publication/test-blog-news-token')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Blog & News publication cron completed.',
                'activated' => 1,
                'deactivated_scheduled' => 0,
            ]);
    }

    public function test_cron_endpoint_rejects_invalid_token(): void
    {
        config(['services.blog_news.publication_cron_token' => 'test-blog-news-token']);

        $this->get('/cron/blog-news/publication/wrong-token')
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Invalid cron token.',
            ]);
    }

    private function createBlogNews(string $slug, array $attributes = []): BlogNews
    {
        return BlogNews::create(array_merge([
            'type' => 'blog',
            'title' => str($slug)->replace('-', ' ')->title()->toString(),
            'slug' => $slug,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ], $attributes));
    }
}
