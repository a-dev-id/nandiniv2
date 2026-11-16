<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Page;
use Illuminate\Support\Collection;

final class AccommodationStructuredData
{
    public static function listing(Page $page, Collection $accommodations): array
    {
        $url = SeoStructuredData::MAIN.'/'.$page->slug;
        $rooms = $accommodations->map(fn (Accommodation $room): array => self::room($room))->all();
        $items = array_map(fn (array $room): array => [
            'name' => $room['name'],
            'url' => $room['url'],
            'item' => ['@id' => $room['@id']],
        ], $rooms);
        // Retain Jungle Villas' existing WebPage node and identifier.
        $data = SeoStructuredData::collection($url, $page->title, $page->meta_description, $items,
            $page->slug === 'jungle-villas' ? 'WebPage' : 'CollectionPage');
        $data['@graph'] = array_merge($data['@graph'], $rooms);
        if ($page->slug === 'the-royal-suites') {
            $breadcrumb = SeoStructuredData::breadcrumb($url, $page->title);
            unset($breadcrumb['@context']);
            $data['@graph'][] = $breadcrumb;
            $data['@graph'][0]['breadcrumb'] = ['@id' => $breadcrumb['@id']];
        }

        return $data;
    }

    private static function room(Accommodation $room): array
    {
        $url = SeoStructuredData::MAIN.($room->slug === 'presidential-royal-suite'
            ? '/presidential-royal-suite'
            : '/'.$room->url_prefix.'/'.$room->slug);
        $images = collect([$room->card_image, $room->hero_image])
            ->merge($room->activeImages->pluck('image'))
            ->map(fn (?string $image): ?string => SeoStructuredData::image($image))
            ->filter()->unique()->values()->all();

        return array_filter([
            '@type' => $room->accommodation_type === 'suite' ? 'Suite' : 'HotelRoom',
            '@id' => $url.'#accommodation',
            'url' => $url,
            'name' => $room->title,
            'description' => SeoStructuredData::text($room->description ?: $room->excerpt),
            'image' => $images,
            'containedInPlace' => SeoStructuredData::hotel(),
            'bed' => SeoStructuredData::text($room->bed_type),
            // Keep free-text occupancy verbatim rather than guessing a maximum.
            'occupancy' => filled($room->occupancy) ? [
                '@type' => 'QuantitativeValue',
                'value' => SeoStructuredData::text($room->occupancy),
            ] : null,
            'amenityFeature' => $room->features->filter(fn ($feature): bool => filled($feature->label))
                ->map(fn ($feature): array => [
                    '@type' => 'LocationFeatureSpecification',
                    'name' => SeoStructuredData::text($feature->label),
                    'value' => true,
                ])->values()->all(),
        ], fn ($value): bool => $value !== null && $value !== '' && $value !== []);
    }
}
