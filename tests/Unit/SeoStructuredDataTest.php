<?php

namespace Tests\Unit;

use App\Models\DiningExperience;
use App\Models\DiningSetting;
use App\Support\DiningStructuredData;
use App\Support\SeoStructuredData;
use Tests\TestCase;

class SeoStructuredDataTest extends TestCase
{
    public function test_breadcrumbs_have_real_production_parents_and_ordered_positions(): void
    {
        $data = SeoStructuredData::breadcrumb('https://nandinibali.com/jungle-villas/jungle-view-villa', 'Jungle View Villa', 'https://nandinibali.com/jungle-villas', 'Jungle Villas');
        $this->assertSame([1, 2, 3], array_column($data['itemListElement'], 'position'));
        $this->assertSame('https://nandinibali.com/', $data['itemListElement'][0]['item']);
        $this->assertSame('Jungle Villas', $data['itemListElement'][1]['name']);
    }

    public function test_image_urls_handle_stored_paths_absolute_urls_and_missing_images(): void
    {
        $this->assertNull(SeoStructuredData::image(null));
        $this->assertSame('https://nandinibali.com/storage/blog/test.webp', SeoStructuredData::image('blog/test.webp'));
        $this->assertSame('https://nandinibali.com/storage/blog/test.webp', SeoStructuredData::image('/storage/blog/test.webp'));
        $this->assertSame('https://nandinibali.com/storage/images/blog/test.jpg', SeoStructuredData::image('images/blog/test.jpg'));
        $this->assertSame('https://cdn.example.com/test.webp', SeoStructuredData::image('https://cdn.example.com/test.webp'));
    }

    public function test_restaurant_does_not_invent_hours_ratings_or_other_venues(): void
    {
        $settings = new DiningSetting(['reservation_url' => 'https://wa.me/6281236871170', 'food_menu_url' => 'https://example.com/menu', 'opening_hours' => 'Conflicting unverified hours']);
        $restaurant = new DiningExperience(['slug' => 'wild-ginger-restaurant', 'card_title' => 'Wild Ginger Restaurant']);
        $data = DiningStructuredData::make($settings, $restaurant);
        $entity = $data['@graph'][1];
        $this->assertSame('Restaurant', $entity['@type']);
        $this->assertSame('Wild Ginger Restaurant', $entity['name']);
        $this->assertSame('80571', $entity['address']['postalCode']);
        $this->assertArrayNotHasKey('openingHours', $entity);
        $this->assertArrayNotHasKey('aggregateRating', $entity);
        $this->assertCount(2, $data['@graph']);
        $this->assertCount(1, DiningStructuredData::make(null, null)['@graph']);
    }

    public function test_json_ld_escapes_script_closing_tags(): void
    {
        $html = view('components.seo.json-ld', ['data' => SeoStructuredData::page('https://nandinibali.com/faq', '</script><script>alert(1)</script>')])->render();
        $this->assertSame(1, substr_count($html, '</script>'));
        $this->assertStringContainsString('\\u003C', $html);
    }
}
