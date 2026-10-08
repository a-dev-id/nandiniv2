<?php

namespace App\Support;

use App\Models\SpaSetting;

final class SpaWellnessJourneys
{
    /**
     * @return array<int, array<string, string|null>>
     */
    public static function items(?SpaSetting $settings): array
    {
        $items = $settings?->wellness_journeys_items ?? [];

        if (filled($items)) {
            return $items;
        }

        $reservationUrl = filled($settings?->reservation_url)
            ? trim((string) $settings->reservation_url)
            : 'https://wa.me/6281236871170';

        return [
            [
                'title' => '2-DAY BALINESE WELLNESS ESCAPE',
                'description' => 'A two-day journey to revive your energy through a curated blend of Balinese massage, herbal rituals and time in nature.',
                'image' => 'spas/hero/68d37345-f6e6-4f1d-a962-725cf049fe62.webp',
                'image_alt' => 'Balinese massage treatment surrounded by the Nandini jungle',
                'details_label' => 'MORE DETAILS',
                'details_url' => '/spa-wellness/2-day-balinese-wellness-escape',
                'book_label' => 'BOOK NOW',
                'book_url' => $reservationUrl,
            ],
            [
                'title' => '3-DAY INNER HARMONY RETREAT',
                'description' => 'A three-day retreat to restore balance and reconnect with yourself through signature treatments, holistic therapies and mindful rituals.',
                'image' => 'spas/hero/b5490cd9-d622-4ce2-b483-992ef4ea0c3c.webp',
                'image_alt' => 'Jungle spa treatment beds prepared for an inner harmony retreat',
                'details_label' => 'MORE DETAILS',
                'details_url' => '/spa-wellness/3-day-inner-harmony-retreat',
                'book_label' => 'BOOK NOW',
                'book_url' => $reservationUrl,
            ],
            [
                'title' => '4-DAY DEEP BALINESE WELLNESS IMMERSION',
                'description' => 'A four-day immersive experience designed for deep relaxation and renewal, with a combination of traditional therapies, wellness rituals and personalised care.',
                'image' => 'spas/hero/19d9f7d8-6a93-422d-8a13-b333a2384ff8.webp',
                'image_alt' => 'Flower bath ritual for a deep Balinese wellness immersion',
                'details_label' => 'MORE DETAILS',
                'details_url' => '/spa-wellness/4-day-deep-balinese-wellness-immersion',
                'book_label' => 'BOOK NOW',
                'book_url' => $reservationUrl,
            ],
        ];
    }

    /**
     * @return array<string, string|null>|null
     */
    public static function findBySlug(string $slug, ?SpaSetting $settings): ?array
    {
        foreach (self::items($settings) as $journey) {
            if (self::slug($journey) === $slug) {
                return $journey;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $journey
     */
    public static function slug(array $journey): ?string
    {
        $path = parse_url((string) ($journey['details_url'] ?? ''), PHP_URL_PATH);

        if (blank($path) || ! str_starts_with((string) $path, '/spa-wellness/')) {
            return null;
        }

        return basename((string) $path);
    }
}
