<?php

namespace Tests\Feature;

use App\Models\BlogNews;
use App\Models\Experience;
use App\Models\Offer;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemovedContentRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_offer_redirects_to_offers_index(): void
    {
        $this->get('/offer/removed-offer')
            ->assertMovedPermanently()
            ->assertRedirect(route('offers.index'));
    }

    public function test_unpublished_offer_redirects_to_offers_index(): void
    {
        Offer::create([
            'title' => 'Removed Offer',
            'slug' => 'removed-offer',
            'is_active' => false,
            'sort_order' => 1,
        ]);

        $this->get('/offer/removed-offer')
            ->assertMovedPermanently()
            ->assertRedirect(route('offers.index'));
    }

    public function test_missing_experience_redirects_to_experiences_index(): void
    {
        $this->get('/experience/day-pass')
            ->assertMovedPermanently()
            ->assertRedirect(route('experiences.index'));
    }

    public function test_unpublished_experience_redirects_to_experiences_index(): void
    {
        Experience::create([
            'title' => 'Day Pass',
            'slug' => 'day-pass',
            'is_active' => false,
            'sort_order' => 1,
        ]);

        $this->get('/experience/day-pass')
            ->assertMovedPermanently()
            ->assertRedirect(route('experiences.index'));
    }

    public function test_missing_blog_post_redirects_to_blog_index(): void
    {
        $this->createBlogIndexPage();

        $this->get('/blog-news/removed-story')
            ->assertMovedPermanently()
            ->assertRedirect(route('blog.index'));
    }

    public function test_unpublished_blog_post_redirects_to_blog_index(): void
    {
        $this->createBlogIndexPage();

        BlogNews::create([
            'type' => 'blog',
            'title' => 'Removed Story',
            'slug' => 'removed-story',
            'is_active' => false,
            'sort_order' => 1,
        ]);

        $this->get('/blog-news/removed-story')
            ->assertMovedPermanently()
            ->assertRedirect(route('blog.index'));
    }

    public function test_missing_cms_page_redirects_home(): void
    {
        $this->get('/removed-page')
            ->assertMovedPermanently()
            ->assertRedirect(route('home'));
    }

    private function createBlogIndexPage(): void
    {
        $page = new Page([
            'title' => 'Blog & News',
            'slug' => 'blog-news',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $page->id = 15;
        $page->save();
    }
}
