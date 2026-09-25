<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SECTION_KEYS = [
        'about_story_hero',
        'about_story_origins',
        'about_story_timeline',
        'about_story_chapter',
        'about_story_chapter_reverse',
        'about_story_comparison',
        'about_story_growth',
        'about_story_mosaic',
        'about_story_gallery',
        'about_story_values',
        'about_story_today',
        'about_story_final',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_images')) {
            return;
        }

        $page = DB::table('pages')
            ->where('slug', 'about-us')
            ->when(Schema::hasColumn('pages', 'site'), fn ($query) => $query->where('site', 'main'))
            ->first();

        if (! $page) {
            return;
        }

        $sections = $this->sections();
        $now = now();

        foreach ($sections as $sortOrder => $section) {
            $exists = DB::table('page_sections')
                ->where('page_id', $page->id)
                ->where('section_key', $section['section_key'])
                ->exists();

            if ($exists) {
                continue;
            }

            $images = $section['images'] ?? [];
            unset($section['images']);

            $sectionId = DB::table('page_sections')->insertGetId([
                'page_id' => $page->id,
                'section_key' => $section['section_key'],
                'title' => $section['title'] ?? null,
                'subtitle' => $section['subtitle'] ?? null,
                'excerpt' => $section['excerpt'] ?? null,
                'description' => $section['description'] ?? null,
                'items' => isset($section['items']) ? json_encode($section['items'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'button_label' => $section['button_label'] ?? null,
                'button_link_type' => $section['button_link_type'] ?? 'manual',
                'button_url' => $section['button_url'] ?? null,
                'button_route' => $section['button_route'] ?? null,
                'text_align' => 'left',
                'background_color' => null,
                'is_active' => true,
                'sort_order' => $sortOrder + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($images as $imageSortOrder => $image) {
                DB::table('page_section_images')->insert([
                    'page_section_id' => $sectionId,
                    'image' => $image['image'] ?? null,
                    'image_file_name' => null,
                    'image_alt' => $image['image_alt'] ?? null,
                    'mobile_image' => $image['mobile_image'] ?? null,
                    'mobile_image_file_name' => null,
                    'mobile_image_alt' => $image['mobile_image_alt'] ?? null,
                    'caption' => $image['caption'] ?? null,
                    'is_active' => true,
                    'sort_order' => $imageSortOrder,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $pageIds = DB::table('pages')->where('slug', 'about-us')->pluck('id');

        DB::table('page_sections')
            ->whereIn('page_id', $pageIds)
            ->whereIn('section_key', self::SECTION_KEYS)
            ->delete();
    }

    private function sections(): array
    {
        return [
            [
                'section_key' => 'about_story_hero',
                'title' => "Rooted in the Jungle\nSince 2005",
                'subtitle' => 'Our Story',
                'excerpt' => 'Susut, Payangan · Bali',
                'description' => '<p>What began as an intimate hillside retreat has evolved over two decades into Nandini Jungle by Hanging Gardens — while remaining deeply connected to the landscape, community and traditions that shaped its beginning.</p>',
                'button_label' => 'Discover Our Story',
                'button_url' => '#our-story',
                'images' => [
                    [
                        'image' => 'https://nandinibali.com/storage/images/gallery/pool%20okl.jpg',
                        'image_alt' => 'Nandini Jungle infinity pool surrounded by tropical rainforest in Ubud, Bali',
                    ],
                ],
            ],
            [
                'section_key' => 'about_story_origins',
                'title' => "A Chance Encounter\nThat Became Nandini",
                'subtitle' => 'Where It Began',
                'description' => '<p>In the late 1990s and early 2000s, Swedish entrepreneur Magnus Falk frequently travelled through the Ubud region. While exploring the area by bicycle, a chance meeting with a local villager led him to the hillside that would eventually become Nandini.</p><p>The steep terrain was challenging, but instead of removing the character of the landscape, the resort was built into it — allowing villas, pathways and tropical vegetation to follow the natural contours of the hillside.</p>',
                'items' => [[
                    'quote' => 'I like to think that the land came to me rather than the other way around.',
                    'attribution' => 'Magnus Falk',
                ]],
                'images' => [
                    [
                        'image' => 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg',
                        'image_alt' => 'Jungle View Villa at Nandini Jungle by Hanging Gardens',
                    ],
                    [
                        'image' => 'https://nandinibali.com/storage/images/gallery/Nandini%20Funicular%20-%20Gondola.jpg',
                        'image_alt' => 'Nandini Jungle funicular travelling through the rainforest',
                    ],
                ],
            ],
            [
                'section_key' => 'about_story_timeline',
                'title' => 'A Story in Time',
                'subtitle' => 'Two Decades of Nandini',
                'items' => [
                    ['year' => 'Late 1990s', 'title' => 'The Beginning', 'description' => 'A chance encounter during Magnus Falk’s travels through the Ubud region leads to the land that would become Nandini.'],
                    ['year' => '2005', 'title' => 'Nandini Opens', 'description' => 'Nandini opens as an intimate hillside retreat.'],
                    ['year' => '2007', 'title' => 'The River', 'description' => 'Access through the jungle is created to reach the secluded riverside below the resort.'],
                    ['year' => '2019', 'title' => 'A New Chapter', 'description' => 'Nandini thoughtfully expands while preserving the character of the original hillside retreat.'],
                    ['year' => '2022', 'title' => 'Renewing Nandini', 'description' => 'Major renovation and upgrades refresh villas and resort facilities.'],
                    ['year' => '2024', 'title' => 'New & Upgraded', 'description' => 'Royal Suites and new dining and wellness experiences mark another evolution.'],
                    ['year' => 'Today', 'title' => 'The Story Continues', 'description' => 'Nandini continues to evolve in harmony with the jungle.'],
                ],
            ],
            [
                'section_key' => 'about_story_chapter',
                'title' => "The Beginning.\nOne Remarkable Hillside.",
                'subtitle' => 'The Beginning',
                'excerpt' => '2005',
                'description' => '<p>Nandini officially opened in 2005 as an intimate retreat built along the steep jungle hillside. Inspired by traditional Balinese architecture, the original villas were created to sit naturally within their surroundings.</p>',
                'items' => [],
                'images' => [[
                    'image' => 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg',
                    'image_alt' => 'Original-style Jungle View Villa on Nandini’s hillside',
                ]],
            ],
            [
                'section_key' => 'about_story_chapter_reverse',
                'title' => "The Journey\nDown to the River",
                'subtitle' => 'A Hidden World Below',
                'excerpt' => '2007',
                'description' => '<p>When Nandini first opened, the river far below the resort was not yet part of the guest experience. Access was created in 2007 through a series of steep steps descending through the jungle, with a lift later assisting guests for part of the journey.</p>',
                'button_label' => 'Explore Nandini Experiences',
                'button_link_type' => 'route',
                'button_route' => 'experiences.index',
                'images' => [[
                    'image' => 'https://nandinibali.com/storage/images/gallery/yoga-by-the-river.jpg',
                    'image_alt' => 'Yoga and wellness by the river at Nandini Jungle',
                ]],
            ],
            [
                'section_key' => 'about_story_comparison',
                'title' => 'The Evolution of Nandini',
                'subtitle' => 'Then & Now',
                'description' => '<p>The resort has changed considerably since opening in 2005, but the landscape that shaped Nandini remains at the heart of its identity.</p>',
                'items' => [
                    ['label' => 'Then', 'title' => 'The Early Years', 'description' => 'Nandini began as an intimate collection of hillside villas, shaped by the jungle and rooted in Balinese character.'],
                    ['label' => 'Now', 'title' => 'Nandini Today', 'description' => 'The resort continues to evolve in harmony with the jungle.'],
                ],
                'images' => [
                    ['image_alt' => 'Reserved for a genuine historical Nandini archive image'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/pool.jpg', 'image_alt' => 'Nandini Jungle pool today'],
                ],
            ],
            [
                'section_key' => 'about_story_growth',
                'title' => "Growing Without\nLosing the Landscape",
                'subtitle' => 'A New Chapter',
                'excerpt' => '2019',
                'description' => '<p>The 2019 expansion marked one of the most significant chapters in Nandini’s history, introducing new accommodation while retaining the jungle setting and character of the original hillside retreat.</p>',
                'items' => [],
                'images' => [[
                    'image' => 'https://nandinibali.com/storage/images/gallery/Mystical%20Jungle%20Pool.jpg',
                    'image_alt' => 'Mystical jungle pool at Nandini Jungle by Hanging Gardens',
                ]],
            ],
            [
                'section_key' => 'about_story_mosaic',
                'title' => 'Renewing the Nandini Experience',
                'subtitle' => 'A Major Renewal',
                'excerpt' => '2022',
                'description' => '<p>A major renovation and upgrade refreshed the original villas and expanded spaces throughout the resort, including enhancements to the lounge together with additions such as the pool bar, riverside yoga area, gym and wedding chapel.</p>',
                'images' => [
                    ['image' => 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg', 'image_alt' => 'Renovated Jungle Villa', 'caption' => 'Jungle Villa'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/JUNGLE%20POOL%20BAR%20%283%29.jpg', 'image_alt' => 'Jungle Pool Bar', 'caption' => 'Pool Bar'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/DJIWA%20SHALA%20%284%29.jpg', 'image_alt' => 'Djiwa Shala yoga pavilion', 'caption' => 'Djiwa Shala'],
                    ['image' => 'https://nandinibali.com/storage/gallery/images/heritage-lounge-balinese-dance-nandini-jungle-bali.webp', 'image_alt' => 'Balinese dance at Heritage Lounge', 'caption' => 'Heritage Lounge'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/Nandini%20Funicular%20-%20Gondola.jpg', 'image_alt' => 'Nandini Jungle funicular', 'caption' => 'Funicular'],
                ],
            ],
            [
                'section_key' => 'about_story_gallery',
                'title' => 'New & Upgraded Nandini',
                'subtitle' => 'The Next Chapter',
                'excerpt' => '2024',
                'description' => '<p>Nandini introduced another significant evolution of the resort experience, with upgraded accommodation and expanded dining and wellness concepts alongside the original Jungle Villas.</p>',
                'items' => [
                    ['eyebrow' => 'Royal Suites', 'title' => 'Jungle Living'],
                    ['eyebrow' => 'Wine Cellar', 'title' => 'A New Dining Chapter'],
                    ['eyebrow' => 'Wine Spa', 'title' => 'Wellness Reimagined'],
                    ['eyebrow' => 'Fitness Centre', 'title' => 'Wellness in Motion'],
                ],
                'images' => [
                    ['image' => 'https://nandinibali.com/storage/images/gallery/teras%20bed.jpg', 'image_alt' => 'Royal Suite terrace and bedroom'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/wine%203.jpg', 'image_alt' => 'Wine cellar dining experience'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/berendam%20wine.jpg', 'image_alt' => 'Wine spa experience'],
                    ['image' => 'https://nandinibali.com/storage/images/blog/attachment/jGWBRtzYE4r3WceXgxiXXylUJZNXOq9bJcIbHgAP.jpg', 'image_alt' => 'Gym and fitness centre at Nandini Jungle by Hanging Gardens'],
                ],
            ],
            [
                'section_key' => 'about_story_values',
                'title' => 'What Has Never Changed',
                'subtitle' => 'Beyond the Years',
                'description' => '<p>Nandini has evolved, but the principles behind it remain closely connected to its surroundings.</p>',
                'items' => [
                    ['value' => '01', 'title' => 'The Land', 'description' => 'Respecting the jungle landscape and allowing architecture to follow the natural hillside.'],
                    ['value' => '02', 'title' => 'The Community', 'description' => 'Working with local team members and communities whose knowledge and traditions remain part of the Nandini experience.'],
                    ['value' => '03', 'title' => 'Balinese Character', 'description' => 'Natural materials, craftsmanship and Balinese traditions continue to influence the way Nandini looks, feels and welcomes its guests.'],
                ],
                'images' => [
                    ['image' => 'https://nandinibali.com/storage/images/gallery/JUNGLE%20POOL%20%288%29.jpg', 'image_alt' => 'Nandini’s architecture within the jungle landscape'],
                    ['image' => 'https://nandinibali.com/storage/gallery/images/heritage-lounge-balinese-dance-nandini-jungle-bali.webp', 'image_alt' => 'Balinese community and cultural performance at Nandini'],
                    ['image' => 'https://nandinibali.com/storage/images/gallery/DJIWA%20SHALA%20%284%29.jpg', 'image_alt' => 'Balinese-inspired Djiwa Shala architecture'],
                ],
            ],
            [
                'section_key' => 'about_story_today',
                'title' => 'The Story Continues',
                'subtitle' => 'Nandini Today',
                'description' => '<p>From the original hillside villas to contemporary Royal Suites, riverside wellness, dining and immersive jungle experiences, today’s Nandini continues the story that began more than two decades ago.</p>',
                'items' => [
                    ['kind' => 'link', 'eyebrow' => 'Stay', 'title' => 'Jungle Villas & Royal Suites', 'url' => '/jungle-villas'],
                    ['kind' => 'link', 'eyebrow' => 'Experience', 'title' => 'Nandini Experiences', 'url' => '/experiences'],
                    ['kind' => 'link', 'eyebrow' => 'Explore', 'title' => 'Nandini Gallery', 'url' => '/gallery'],
                ],
                'images' => [[
                    'image' => 'https://nandinibali.com/storage/images/gallery/pool%20okl.jpg',
                    'image_alt' => 'Nandini Jungle by Hanging Gardens today',
                ]],
            ],
            [
                'section_key' => 'about_story_final',
                'title' => "Be Part of\nOur Continuing Story",
                'subtitle' => 'The Next Chapter',
                'description' => '<p>More than two decades after opening its doors, Nandini continues to evolve — shaped by the jungle, its people and every guest who becomes part of its story.</p>',
                'items' => [
                    ['label' => 'Plan Your Stay', 'url' => 'https://nandinijunglebyhanginggardens.reserve-online.net/'],
                ],
                'images' => [[
                    'image' => 'https://nandinibali.com/storage/images/gallery/JUNGLE%20POOL%20%288%29.jpg',
                    'image_alt' => 'Nandini Jungle pool surrounded by rainforest',
                ]],
            ],
        ];
    }
};
