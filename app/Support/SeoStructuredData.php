<?php

namespace App\Support;

final class SeoStructuredData
{
    public const MAIN = 'https://nandinibali.com';
    public const DINING = 'https://dining.nandinibali.com';
    public const SPA = 'https://spa.nandinibali.com';

    public static function image(?string $path, string $site = self::MAIN): ?string
    {
        if (blank($path)) {
            return null;
        }
        if (str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'http://')) {
            return 'https://'.substr($path, 7);
        }
        $path = ltrim($path, '/');
        return rtrim($site, '/').'/'.(str_starts_with($path, 'storage/') ? $path : 'storage/'.$path);
    }

    public static function breadcrumb(string $url, string $title, string $parentUrl, string $parentName): array
    {
        $items = [
            ['name' => 'Home', 'item' => self::MAIN.'/'],
            ['name' => $parentName, 'item' => $parentUrl],
            ['name' => $title, 'item' => $url],
        ];
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            '@id' => $url.'#breadcrumb',
            'itemListElement' => array_map(fn (array $item, int $index): array => [
                '@type' => 'ListItem', 'position' => $index + 1,
            ] + $item, $items, array_keys($items)),
        ];
    }

    public static function page(string $url, string $title, ?string $description = null): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => $url.'#webpage',
            'url' => $url,
            'name' => $title,
            'description' => $description ? trim(html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8')) : null,
            'isPartOf' => ['@id' => self::MAIN.'/#website'],
            'about' => ['@id' => self::MAIN.'/#hotel'],
        ], fn ($value): bool => $value !== null && $value !== '');
    }
}
