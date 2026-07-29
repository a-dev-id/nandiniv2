<?php

namespace Tests\Feature;

use App\Models\GuestReview;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestReviewPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_reviews_page_shows_only_active_reviews_with_rich_content(): void
    {
        GuestReview::create([
            'reviewer_name' => 'A Happy Guest',
            'excerpt' => 'A peaceful jungle stay.',
            'review_text' => '<p>A wonderful <strong>jungle experience</strong>.</p>',
            'rating' => 5,
            'source' => 'Tripadvisor',
            'reviewed_at' => '2026-07-10',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        GuestReview::create([
            'reviewer_name' => 'Hidden Guest',
            'excerpt' => 'This review is hidden.',
            'review_text' => '<p>This review should not be public.</p>',
            'rating' => 4,
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('guest-reviews.index'));

        $response
            ->assertOk()
            ->assertSee('Guest Reviews')
            ->assertSee('A Happy Guest')
            ->assertSee('A peaceful jungle stay.')
            ->assertSee('Read More')
            ->assertSee('data-navbar-mode="solid"', false)
            ->assertSee('<strong>jungle experience</strong>', false)
            ->assertDontSee('Hidden Guest');
    }

    public function test_homepage_shows_only_active_featured_reviews(): void
    {
        Page::query()->forceCreate([
            'id' => 1,
            'title' => 'Home',
            'slug' => 'home',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        GuestReview::create([
            'reviewer_name' => 'Featured Guest',
            'excerpt' => 'This featured review belongs on the homepage.',
            'review_text' => '<p>Featured full review.</p>',
            'rating' => 5,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        GuestReview::create([
            'reviewer_name' => 'Regular Guest',
            'excerpt' => 'This review belongs only on the reviews page.',
            'review_text' => '<p>Regular full review.</p>',
            'rating' => 5,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Featured Guest')
            ->assertDontSee('Regular Guest');
    }
}
