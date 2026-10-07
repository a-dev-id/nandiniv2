<?php

namespace App\Support;

use App\Models\Page;

final class HomepageStructuredData
{
    /** @return array<string, mixed> */
    public static function make(Page $page): array
    {
        $url = rtrim((string) config('resort.url'), '/');
        $name = (string) config('resort.name');
        $logoUrl = (string) config('resort.logo_url');
        $email = (string) config('resort.email');
        $telephone = (string) config('resort.telephone');
        $sameAs = array_values(config('resort.same_as', []));
        $address = config('resort.address', []);

        $websiteId = $url.'/#website';
        $organizationId = $url.'/#organization';
        $hotelId = $url.'/#hotel';

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $websiteId,
                    'url' => $url,
                    'name' => $name,
                    'publisher' => [
                        '@id' => $organizationId,
                    ],
                ],
                [
                    '@type' => 'Organization',
                    '@id' => $organizationId,
                    'name' => $name,
                    'url' => $url,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoUrl,
                    ],
                    'email' => $email,
                    'telephone' => $telephone,
                    'sameAs' => $sameAs,
                ],
                [
                    '@type' => 'Hotel',
                    '@id' => $hotelId,
                    'name' => $name,
                    'url' => $url,
                    'description' => (string) $page->meta_description,
                    'image' => self::imageUrl((string) $page->hero_image, $url),
                    'logo' => $logoUrl,
                    'telephone' => $telephone,
                    'email' => $email,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => (string) ($address['street_address'] ?? ''),
                        'addressLocality' => (string) ($address['locality'] ?? ''),
                        'addressRegion' => (string) ($address['region'] ?? ''),
                        'postalCode' => (string) ($address['postal_code'] ?? ''),
                        'addressCountry' => (string) ($address['country'] ?? ''),
                    ],
                    'sameAs' => $sameAs,
                ],
            ],
        ];
    }

    private static function imageUrl(string $image, string $baseUrl): string
    {
        $image = trim($image);

        if (str_starts_with($image, 'https://') || str_starts_with($image, 'http://')) {
            return $image;
        }

        $path = ltrim($image, '/');

        if (! str_starts_with($path, 'storage/')) {
            $path = 'storage/'.$path;
        }

        return $baseUrl.'/'.ltrim($path, '/');
    }
}
