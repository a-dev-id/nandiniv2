<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JsonException;
use Tests\TestCase;

class HomepageSubtitleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_the_current_database_subtitle(): void
    {
        Page::query()->whereKey(1)->delete();

        $page = new Page();
        $page->setAttribute('id', 1);
        $page->forceFill([
            'site' => Page::SITE_MAIN,
            'page_name' => 'Home Page',
            'title' => 'Nandini Jungle by Hanging Gardens',
            'slug' => 'home',
            'subtitle' => 'A Luxury Jungle Resort in Payangan, Greater Ubud, Bali',
            'description' => '<p>Database-managed homepage introduction.</p>',
            'hero_image' => 'pages/hero/74e30cb4-6fec-4883-8b57-c474baa71740.webp',
            'meta_title' => 'Nandini Jungle by Hanging Gardens | Luxury Resort Ubud Bali',
            'meta_description' => 'Nandini Jungle by Hanging Gardens is a luxury jungle resort in Payangan, Ubud, Bali with private villas, Royal Suites, spa, dining and Ayung River views.',
            'is_active' => true,
        ]);
        $page->save();

        $response = $this->get('http://nandinibali.test/');

        $response
            ->assertOk()
            ->assertViewIs('pages.home')
            ->assertSee('<title>Nandini Jungle by Hanging Gardens | Luxury Resort Ubud Bali</title>', false)
            ->assertSee('<meta name="description" content="Nandini Jungle by Hanging Gardens is a luxury jungle resort in Payangan, Ubud, Bali with private villas, Royal Suites, spa, dining and Ayung River views.">', false)
            ->assertSee('<link rel="canonical" href="http://nandinibali.test">', false)
            ->assertSee('A Luxury Jungle Resort in Payangan, Greater Ubud, Bali')
            ->assertSee('Database-managed homepage introduction.', false);

        $this->assertSame(1, substr_count($response->getContent(), '<h1'));
        $this->assertSame(1, substr_count($response->getContent(), 'A Luxury Jungle Resort in Payangan, Greater Ubud, Bali'));
    }

    /** @throws JsonException */
    public function test_homepage_renders_one_valid_linked_entity_graph(): void
    {
        Page::query()->whereKey(1)->delete();

        $page = new Page();
        $page->setAttribute('id', 1);
        $page->forceFill([
            'site' => Page::SITE_MAIN,
            'page_name' => 'Home Page',
            'title' => 'Nandini Jungle by Hanging Gardens',
            'slug' => 'home',
            'subtitle' => 'A Luxury Jungle Resort in Payangan, Greater Ubud, Bali',
            'description' => '<p>Homepage introduction.</p>',
            'hero_image' => 'pages/hero/74e30cb4-6fec-4883-8b57-c474baa71740.webp',
            'meta_title' => 'Nandini Jungle by Hanging Gardens | Luxury Resort Ubud Bali',
            'meta_description' => 'Nandini Jungle by Hanging Gardens is a luxury jungle resort in Payangan, Ubud, Bali with private villas, Royal Suites, spa, dining and Ayung River views.',
            'is_active' => true,
        ]);
        $page->save();

        $content = $this->get('http://nandinibali.test/')
            ->assertOk()
            ->getContent();

        $this->assertSame(1, preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $content,
            $matches,
        ));

        $schema = json_decode($matches[1][0], true, 512, JSON_THROW_ON_ERROR);
        $graph = collect($schema['@graph']);

        $this->assertSame('https://schema.org', $schema['@context']);
        $this->assertSame(['WebSite', 'Organization', 'Hotel'], $graph->pluck('@type')->all());
        $this->assertSame([
            'https://nandinibali.com/#website',
            'https://nandinibali.com/#organization',
            'https://nandinibali.com/#hotel',
        ], $graph->pluck('@id')->all());
        $this->assertCount(3, $graph->pluck('@id')->unique());

        $website = $graph->firstWhere('@type', 'WebSite');
        $organization = $graph->firstWhere('@type', 'Organization');
        $hotel = $graph->firstWhere('@type', 'Hotel');

        $this->assertSame('https://nandinibali.com', $website['url']);
        $this->assertSame('Nandini Jungle by Hanging Gardens', $website['name']);
        $this->assertSame('https://nandinibali.com/#organization', $website['publisher']['@id']);
        $this->assertSame('https://nandinibali.com/images/logo-njhg.png', $organization['logo']['url']);
        $this->assertSame('reservation@nandinibali.com', $organization['email']);
        $this->assertSame('+623618983111', $organization['telephone']);
        $this->assertCount(4, $organization['sameAs']);
        $this->assertSame('https://nandinibali.com/storage/pages/hero/74e30cb4-6fec-4883-8b57-c474baa71740.webp', $hotel['image']);
        $this->assertSame($page->meta_description, $hotel['description']);
        $this->assertSame('https://nandinibali.com/images/logo-njhg.png', $hotel['logo']);
        $this->assertSame('reservation@nandinibali.com', $hotel['email']);
        $this->assertSame('+623618983111', $hotel['telephone']);
        $this->assertSame('Banjar Susut, Desa Buahan', $hotel['address']['streetAddress']);
        $this->assertSame('Payangan', $hotel['address']['addressLocality']);
        $this->assertSame('Bali', $hotel['address']['addressRegion']);
        $this->assertSame('80571', $hotel['address']['postalCode']);
        $this->assertSame('ID', $hotel['address']['addressCountry']);
        $this->assertSame($organization['sameAs'], $hotel['sameAs']);

        foreach ([
            'aggregateRating',
            'review',
            'starRating',
            'priceRange',
            'numberOfRooms',
            'checkinTime',
            'checkoutTime',
            'latitude',
            'longitude',
            'amenityFeature',
            'petsAllowed',
        ] as $unsupportedProperty) {
            $this->assertArrayNotHasKey($unsupportedProperty, $hotel);
        }
    }
}
