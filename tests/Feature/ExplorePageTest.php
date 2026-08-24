<?php

namespace Tests\Feature;

use App\Models\BlogNews;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExplorePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_explore_page_shows_only_published_blog_and_news_records_in_cms_order(): void
    {
        Page::create([
            'title' => 'Explore Nandini',
            'slug' => 'home',
            'is_active' => true,
        ]);

        $this->createBlogNews([
            'type' => 'news',
            'title' => 'Resort News',
            'slug' => 'resort-news',
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $this->createBlogNews([
            'type' => 'blog',
            'title' => 'Jungle Journal',
            'slug' => 'jungle-journal',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $this->createBlogNews([
            'title' => 'Unfeatured Story',
            'slug' => 'unfeatured-story',
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        $this->createBlogNews([
            'title' => 'Future Story',
            'slug' => 'future-story',
            'published_at' => now()->addDay(),
            'sort_order' => 0,
        ]);

        $this->createBlogNews([
            'title' => 'Inactive Story',
            'slug' => 'inactive-story',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $response = $this->get('https://'.config('domains.main').'/explore');

        $response
            ->assertOk()
            ->assertSee('Blog &amp; News', false)
            ->assertSeeInOrder(['Jungle Journal', 'Resort News'])
            ->assertDontSee('Unfeatured Story')
            ->assertDontSee('Future Story')
            ->assertDontSee('Inactive Story');
    }

    public function test_published_news_link_opens_the_shared_blog_and_news_detail_page(): void
    {
        $page = new Page([
            'title' => 'Blog & News',
            'slug' => 'blog-news',
            'is_active' => true,
        ]);
        $page->id = 15;
        $page->save();

        $this->createBlogNews([
            'type' => 'news',
            'title' => 'Resort News',
            'slug' => 'resort-news',
        ]);

        $this->get('https://'.config('domains.main').'/blog-news/resort-news')
            ->assertOk()
            ->assertSee('Resort News')
            ->assertSee('Share this article');
    }

    private function createBlogNews(array $attributes): BlogNews
    {
        return BlogNews::create(array_merge([
            'type' => 'blog',
            'title' => 'Published Story',
            'slug' => 'published-story',
            'published_at' => now()->subDay(),
            'is_active' => true,
            'sort_order' => 1,
        ], $attributes));
    }
}
