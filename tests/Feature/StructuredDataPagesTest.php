<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\AccommodationFeature;
use App\Models\DiningExperience;
use App\Models\Offer;
use App\Models\Page;
use App\Support\OfferStructuredData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructuredDataPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_accommodation_listings_render_only_the_rooms_they_display(): void
    {
        $this->page('jungle-villas', 'Jungle Villas');
        $this->page('the-royal-suites', 'Royal Suites');
        $villa = Accommodation::create([
            'title' => 'Jungle View Villa', 'slug' => 'jungle-view-villa',
            'accommodation_type' => 'villa', 'is_active' => true,
            'description' => '<p>A forest retreat.</p>', 'occupancy' => '2 Adults + 1 Child',
            'bed_type' => 'King-size bed', 'hero_image' => 'rooms/villa.webp',
        ]);
        $villa->images()->create(['image' => 'rooms/gallery.webp', 'is_active' => true]);
        $feature = AccommodationFeature::create(['label' => 'Private Balcony', 'is_active' => true]);
        $villa->features()->attach($feature);
        Accommodation::create([
            'title' => 'Royal Suite', 'slug' => 'royal-suite',
            'accommodation_type' => 'suite', 'is_active' => true,
        ]);
        Accommodation::create([
            'title' => 'Presidential Royal Suite', 'slug' => 'presidential-royal-suite',
            'accommodation_type' => 'suite', 'is_active' => true,
        ]);
        Accommodation::create([
            'title' => 'Hidden Villa', 'slug' => 'hidden-villa',
            'accommodation_type' => 'villa', 'is_active' => false,
        ]);

        $villaGraph = $this->nodes($this->get('http://nandinibali.test/jungle-villas')->assertOk()->getContent());
        $this->assertCount(3, $villaGraph);
        $this->assertSame('WebPage', $villaGraph[0]['@type']);
        $this->assertSame('https://nandinibali.com/jungle-villas#webpage', $villaGraph[0]['@id']);
        $room = $villaGraph[2];
        $this->assertSame('HotelRoom', $room['@type']);
        $this->assertSame('2 Adults + 1 Child', $room['occupancy']['value']);
        $this->assertSame('King-size bed', $room['bed']);
        $this->assertSame(['https://nandinibali.com/storage/rooms/villa.webp', 'https://nandinibali.com/storage/rooms/gallery.webp'], $room['image']);
        $this->assertSame('Private Balcony', $room['amenityFeature'][0]['name']);
        $this->assertSame('Hotel', $room['containedInPlace']['@type']);
        $this->assertSame('https://nandinibali.com/#hotel', $room['containedInPlace']['@id']);

        $suiteGraph = $this->nodes($this->get('http://nandinibali.test/the-royal-suites')->assertOk()->getContent());
        $this->assertSame(['CollectionPage', 'ItemList', 'Suite', 'BreadcrumbList'], array_column($suiteGraph, '@type'));
        $this->assertCount(1, $suiteGraph[1]['itemListElement']);
        $this->assertSame('Royal Suite', $suiteGraph[2]['name']);
        foreach (['image', 'bed', 'occupancy', 'amenityFeature', 'price'] as $optional) {
            $this->assertArrayNotHasKey($optional, $suiteGraph[2]);
        }
        $this->assertSame([1, 2], array_column($suiteGraph[3]['itemListElement'], 'position'));
    }

    public function test_offers_listing_and_multiple_details_match_publication_and_preserve_breadcrumbs(): void
    {
        $this->travelTo(today()->setDate(2026, 11, 10)->setTime(15, 0));
        $this->page('offers', 'Offers', 2);
        $first = Offer::create([
            'title' => 'Solo Renewal', 'slug' => 'solo-renewal', 'is_active' => true, 'sort_order' => 1,
            'description' => '<p>Retreat &amp; renew.</p><ul><li>Breakfast included.</li><li>Non-refundable.</li></ul>',
            'valid_start_date' => '2026-11-10', 'valid_end_date' => '2026-11-10',
            'booking_url_override' => 'https://nandinijunglebyhanginggardens.reserve-online.net/?rate=123&nights=2',
            'booking_nights' => 2,
        ]);
        $second = Offer::create([
            'title' => 'Jungle Escape', 'slug' => 'jungle-escape', 'is_active' => true, 'sort_order' => 2,
        ]);
        $hidden = [];
        foreach ([
            ['slug' => 'expired', 'valid_end_date' => '2026-11-09'],
            ['slug' => 'future', 'valid_start_date' => '2026-11-11'],
            ['slug' => 'unpublished', 'is_active' => false],
        ] as $attributes) {
            $hidden[] = Offer::create(array_merge(['title' => $attributes['slug'], 'is_active' => true], $attributes));
        }

        $graph = $this->nodes($this->get('http://nandinibali.test/offers')->assertOk()->getContent());
        $this->assertSame(['CollectionPage', 'ItemList'], array_column($graph, '@type'));
        $this->assertSame([1, 2], array_column($graph[1]['itemListElement'], 'position'));
        $this->assertSame(['Solo Renewal', 'Jungle Escape'], array_column($graph[1]['itemListElement'], 'name'));
        $this->assertSame('https://nandinibali.com/offer/solo-renewal', $graph[1]['itemListElement'][0]['url']);

        foreach ([$first, $second] as $offer) {
            $graph = $this->nodes($this->get('http://nandinibali.test/offer/'.$offer->slug)->assertOk()->getContent());
            $this->assertSame(['WebPage', 'Offer', 'BreadcrumbList'], array_column($graph, '@type'));
            $this->assertSame($offer->title, $graph[1]['name']);
            $this->assertSame([1, 2, 3], array_column($graph[2]['itemListElement'], 'position'));
            $this->assertSame('https://nandinibali.com/#hotel', $graph[1]['offeredBy']['@id']);
            foreach (['price', 'priceCurrency', 'availability', 'validFrom', 'validThrough', 'eligibleDuration'] as $unsupported) {
                $this->assertArrayNotHasKey($unsupported, $graph[1]);
            }
            if ($offer->is($first)) {
                $this->assertSame('Retreat & renew. Breakfast included. Non-refundable.', $graph[1]['description']);
                $this->assertSame($offer->booking_url_override, $graph[1]['potentialAction']['target']);
            } else {
                $this->assertArrayNotHasKey('potentialAction', $graph[1]);
                $this->assertArrayNotHasKey('description', $graph[1]);
            }
        }
        foreach ($hidden as $offer) {
            $this->get('http://nandinibali.test/offer/'.$offer->slug)->assertRedirect(route('offers.index'));
            $this->assertSame(['WebPage'], array_column(OfferStructuredData::detail($offer)['@graph'], '@type'));
        }
    }

    public function test_dining_renders_a_typed_hotel_reference_and_spa_preserves_its_service(): void
    {
        DiningExperience::updateOrCreate(['slug' => 'wild-ginger-restaurant'], [
            'card_title' => 'Wild Ginger Restaurant', 'is_active' => true,
        ]);
        $graph = $this->nodes($this->get('https://'.config('domains.dining').'/')->assertOk()->getContent());
        $restaurant = collect($graph)->firstWhere('@type', 'Restaurant');
        $this->assertSame('Wild Ginger Restaurant', $restaurant['name']);
        $this->assertSame('Hotel', $restaurant['containedInPlace']['@type']);
        $this->assertSame('https://nandinibali.com/#hotel', $restaurant['containedInPlace']['@id']);
        $graph = $this->nodes($this->get('https://'.config('domains.spa').'/')->assertOk()->getContent());
        $this->assertSame(['WebPage', 'Service'], array_column($graph, '@type'));
        $this->assertSame('https://nandinibali.com/#hotel', $graph[1]['provider']['@id']);
    }

    public function test_empty_collections_render_and_the_offers_schema_matches_the_visible_limit(): void
    {
        $this->page('offers', 'Offers', 2);
        $this->page('jungle-villas', 'Jungle Villas');
        $this->page('the-royal-suites', 'Royal Suites');
        foreach (['offers', 'jungle-villas', 'the-royal-suites'] as $path) {
            $graph = $this->nodes($this->get('http://nandinibali.test/'.$path)->assertOk()->getContent());
            $this->assertSame([], $graph[1]['itemListElement']);
        }
        for ($index = 1; $index <= 100; $index++) {
            Offer::create(['title' => 'Offer '.$index, 'slug' => 'offer-'.$index, 'is_active' => true, 'sort_order' => $index]);
        }
        $graph = $this->nodes($this->get('http://nandinibali.test/offers')->assertOk()->getContent());
        $this->assertCount(99, $graph[1]['itemListElement']);
        $this->assertSame('Offer 99', $graph[1]['itemListElement'][98]['name']);
    }

    private function page(string $slug, string $title, ?int $id = null): Page
    {
        if ($id) {
            Page::query()->whereKey($id)->delete();
        }
        $page = new Page;
        $page->forceFill(array_filter([
            'id' => $id, 'slug' => $slug, 'title' => $title, 'site' => Page::SITE_MAIN, 'is_active' => true,
        ], fn ($value): bool => $value !== null));
        $page->save();

        return $page;
    }

    private function nodes(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);
        $this->assertNotEmpty($matches[1]);
        $nodes = [];
        foreach ($matches[1] as $json) {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $nodes = array_merge($nodes, $data['@graph'] ?? [$data]);
        }
        $ids = array_column($nodes, '@id');
        $this->assertCount(count(array_unique($ids)), $ids, 'Duplicate top-level schema identifiers');

        return $nodes;
    }
}
