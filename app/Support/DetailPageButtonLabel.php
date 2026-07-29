<?php

namespace App\Support;

final class DetailPageButtonLabel
{
    /**
     * Keep action/listing labels intact and standardize links to individual content pages.
     */
    public static function resolve(?string $label, ?string $routeName = null, ?string $url = null): ?string
    {
        if (blank($label) || ! self::isDetailDestination($routeName, $url)) {
            return $label;
        }

        return 'More Details';
    }

    public static function isDetailDestination(?string $routeName = null, ?string $url = null): bool
    {
        $routeName = trim((string) $routeName);

        if ($routeName !== '' && str_ends_with($routeName, '.show')) {
            return true;
        }

        $path = parse_url(html_entity_decode(trim((string) $url)), PHP_URL_PATH);

        if (! is_string($path) || $path === '') {
            return false;
        }

        $path = '/' . trim($path, '/');

        foreach (self::detailPathPatterns() as $pattern) {
            if (preg_match($pattern, $path) === 1) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    private static function detailPathPatterns(): array
    {
        return [
            '#^/offer/[^/]+$#',
            '#^/experience/[^/]+$#',
            '#^/jungle-villas/[^/]+$#',
            '#^/the-royal-suites?/[^/]+$#',
            '#^/honeymoon/[^/]+$#',
            '#^/spa-wellness/[^/]+$#',
            '#^/holy-river/[^/]+$#',
            '#^/blog-news/[^/]+$#',
        ];
    }
}
