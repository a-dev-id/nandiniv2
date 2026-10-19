<?php

namespace App\Support;

use App\Models\DiningExperience;
use App\Models\DiningSetting;

final class DiningStructuredData
{
    public static function make(?DiningSetting $settings, ?DiningExperience $restaurant): array
    {
        $url = SeoStructuredData::DINING.'/';
        $graph = [SeoStructuredData::page($url, $settings?->meta_title ?: 'Dining at Nandini Jungle', $settings?->meta_description)];
        unset($graph[0]['@context']);
        if ($restaurant) {
            $restaurantUrl = SeoStructuredData::DINING.'/experiences/'.$restaurant->slug;
            $address = config('resort.address');
            $graph[] = array_filter([
                '@type' => 'Restaurant',
                '@id' => $restaurantUrl.'#restaurant',
                'name' => 'Wild Ginger Restaurant',
                'url' => $restaurantUrl,
                'description' => trim(html_entity_decode(strip_tags((string) $restaurant->short_description), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'image' => SeoStructuredData::image($restaurant->card_image ?: $restaurant->hero_image),
                'telephone' => config('resort.telephone'),
                'email' => $settings?->reservation_email ?: config('resort.email'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $address['street_address'],
                    'addressLocality' => $address['locality'],
                    'addressRegion' => $address['region'],
                    'postalCode' => $address['postal_code'],
                    'addressCountry' => $address['country'],
                ],
                'containedInPlace' => ['@id' => SeoStructuredData::MAIN.'/#hotel'],
                'parentOrganization' => ['@id' => SeoStructuredData::MAIN.'/#organization'],
                'hasMenu' => $settings?->visit_food_menu_url ?: $settings?->food_menu_url,
                'acceptsReservations' => $settings?->reservation_url ?: null,
                'servesCuisine' => $settings?->cuisine ?: null,
            ], fn ($value): bool => $value !== null && $value !== '');
            $graph[0]['mainEntity'] = ['@id' => $restaurantUrl.'#restaurant'];
        }
        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
}
