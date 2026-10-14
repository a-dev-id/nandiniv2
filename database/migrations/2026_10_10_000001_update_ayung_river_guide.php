<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SLUG = 'ayung-river-ubud-guide';

    private const META_TITLE = 'Ayung River Ubud Guide: Rafting & River Experiences | Nandini';

    private const META_DESCRIPTION = 'Discover the Ayung River in Ubud, Bali, from white-water rafting and jungle scenery to Balinese purification, Holy River and riverside experiences.';

    public function up(): void
    {
        if (! Schema::hasTable('blog_news') || ! Schema::hasTable('blog_news_sections')) {
            return;
        }

        $blogId = DB::table('blog_news')->where('slug', self::SLUG)->value('id');

        if (! $blogId) {
            return;
        }

        $now = now();

        DB::table('blog_news')->where('id', $blogId)->update([
            'title' => 'Ayung River in Ubud, Bali: Rafting, Nature & River Experiences',
            'excerpt' => 'A practical guide to the Ayung River in Bali, including its Payangan and greater Ubud setting, white-water rafting, Balinese purification and riverside wellness experiences.',
            'description' => '<p>The Ayung River is one of Bali\'s major river systems and a defining natural feature of the Ubud and Payangan landscape. Flowing through deep valleys, tropical vegetation and rural areas, the river is especially well known around Ubud for white-water rafting and nature-based experiences.</p><p>At Nandini Jungle by Hanging Gardens in Payangan, the Ayung River also forms the setting for quieter experiences, including Balinese purification, riverside wellness and moments of reflection surrounded by the jungle.</p>',
            'hero_image_alt' => 'White-water rafting on the Ayung River in Bali',
            'hero_mobile_image_alt' => 'White-water rafting on the Ayung River in Bali',
            'card_image_alt' => 'White-water rafting on the Ayung River in Bali',
            'button_label' => null,
            'button_url' => null,
            'meta_title' => self::META_TITLE,
            'meta_description' => self::META_DESCRIPTION,
            'updated_at' => $now,
        ]);

        $factsId = DB::table('blog_news_sections')
            ->where('blog_news_id', $blogId)
            ->where('title', 'QUICK FACTS ABOUT THE AYUNG RIVER')
            ->value('id');

        $facts = [
            'section_key' => 'intro_text_section',
            'title' => 'QUICK FACTS ABOUT THE AYUNG RIVER',
            'subtitle' => 'AYUNG RIVER AT A GLANCE',
            'excerpt' => null,
            'description' => '<p>The Ayung connects Bali\'s northern uplands with the island\'s south and is closely associated with the river valleys around Payangan and Ubud.</p>',
            'button_label' => null,
            'button_link_type' => 'manual',
            'button_url' => null,
            'button_route' => null,
            'text_align' => 'left',
            'background_color' => 'warm_ivory',
            'items' => json_encode([
                'gtm_section' => 'ayung_facts',
                'presentation' => 'facts',
                'entries' => [
                    ['label' => 'APPROX. 75 KM', 'description' => 'Official Indonesian tourism information describes the Ayung as stretching for about 75 kilometres.'],
                    ['label' => 'NORTH TO SOUTH', 'description' => 'Its course runs from Bali\'s northern mountain area toward the Badung Strait.'],
                    ['label' => 'PAYANGAN LANDSCAPE', 'description' => 'The Ayung River valley is part of Payangan, within the greater Ubud area.'],
                    ['label' => 'RAFTING IN UBUD', 'description' => 'The Ubud section is one of Bali\'s established white-water rafting destinations.'],
                ],
                'source' => [
                    'label' => 'Indonesia Travel, Ministry of Tourism of the Republic of Indonesia',
                    'url' => 'https://www.indonesia.travel/gb/en/travel-ideas/marine/5-reasons-travelers-love-white-water-rafting-at-ayung-river-in-ubud',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'is_active' => true,
            'sort_order' => 1,
            'updated_at' => $now,
        ];

        if ($factsId) {
            DB::table('blog_news_sections')->where('id', $factsId)->update($facts);
        } else {
            $factsId = DB::table('blog_news_sections')->insertGetId(array_merge($facts, [
                'id' => ((int) DB::table('blog_news_sections')->max('id')) + 1,
                'blog_news_id' => $blogId,
                'created_at' => $now,
            ]));
        }

        $this->updateSection($blogId, 'FAQ', [
            'section_key' => 'intro_text_section',
            'title' => 'WHERE IS THE AYUNG RIVER IN BALI?',
            'subtitle' => 'LOCATION',
            'description' => '<p>The Ayung River runs from Bali\'s northern mountain area through the centre of the island and toward the Badung Strait on the south coast. Sections around Payangan and greater Ubud are known for tropical valleys, riverbank scenery and rafting.</p><p>Nandini Jungle by Hanging Gardens is in Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area. It is not in central Ubud town.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'text_align' => 'left',
            'background_color' => 'white',
            'items' => $this->items('ayung_location'),
            'sort_order' => 2,
        ]);

        $this->updateSection($blogId, 'What Makes the Ayung River Special?', [
            'section_key' => 'split_media_section',
            'title' => 'WHAT MAKES THE AYUNG RIVER SPECIAL?',
            'subtitle' => 'LANDSCAPE & EXPERIENCE',
            'description' => '<p>The Ayung is distinguished by its tropical valley landscape, dense vegetation and the contrast between active and slower ways to experience the river. Rafting offers a journey through moving water and changing scenery, while riverside cultural and wellness experiences invite a quieter connection with the same environment.</p><p>Around Payangan, the river also gives Nandini a strong sense of place: the jungle, valley and sound of flowing water shape experiences beside the river without turning the setting into a single-purpose attraction.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'items' => $this->items('ayung_overview'),
            'sort_order' => 3,
        ]);

        $this->updateSection($blogId, 'Ayung River Rafting: The Adventure Side', [
            'section_key' => 'intro_text_section',
            'title' => 'AYUNG RIVER RAFTING IN UBUD',
            'subtitle' => 'WHITE-WATER ADVENTURE',
            'description' => '<p>White-water rafting is one of the best-known activities on the Ayung River. Guided trips use different river sections and routes, so the character and difficulty of an outing depend on the operator, the selected stretch and current river conditions.</p><p>Rainfall can affect water levels and the pace of the river. Guests should review the latest conditions, follow the operator\'s safety briefing and guidance, and choose an experience appropriate to their circumstances. Booking details belong on the dedicated rafting page rather than in this editorial guide.</p>',
            'button_label' => 'EXPLORE AYUNG RIVER RAFTING',
            'button_link_type' => 'manual',
            'button_url' => 'https://nandinibali.com/experience/ayung-white-water-rafting',
            'button_route' => null,
            'items' => $this->items('ayung_rafting', true),
            'sort_order' => 4,
        ]);

        $this->updateSection($blogId, 'Holy River Ubud: A More Peaceful Experience', [
            'section_key' => 'split_media_reverse',
            'title' => 'AYUNG RIVER FOR MELUKAT & BALINESE PURIFICATION',
            'subtitle' => 'BALINESE PURIFICATION',
            'description' => '<p>Melukat is a Balinese purification tradition using water and associated with cleansing, renewal and spiritual reflection. At Nandini\'s Holy River setting beside the Ayung, guided purification and blessing experiences offer a respectful way to encounter this tradition in the surrounding jungle landscape.</p><p>The experience is cultural and reflective rather than a medical treatment or an alternative form of rafting. Guests should approach the ritual with respect and follow the guidance provided by the people leading it.</p>',
            'button_label' => 'EXPLORE THE HOLY RIVER',
            'button_link_type' => 'manual',
            'button_url' => 'https://nandinibali.com/holy-river',
            'button_route' => null,
            'items' => $this->items('holy_river', true),
            'sort_order' => 5,
        ]);

        $this->updateSection($blogId, 'Rafting vs Holy River Experience: Which One Should You Choose?', [
            'section_key' => 'intro_text_section',
            'title' => 'RAFTING VS HOLY RIVER: WHICH EXPERIENCE FITS?',
            'subtitle' => 'TWO WAYS TO EXPERIENCE THE RIVER',
            'description' => '<p>Both experiences are connected to the Ayung River, but they meet different interests. Neither is presented as better; the choice depends on whether you want an active river journey or a quieter cultural experience.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'background_color' => 'warm_ivory',
            'items' => json_encode([
                'gtm_section' => 'rafting_vs_holy_river',
                'presentation' => 'blocks',
                'entries' => [
                    ['title' => 'RAFTING', 'description' => 'An active, guided journey on the river, shaped by the route and current water conditions.'],
                    ['title' => 'HOLY RIVER', 'description' => 'A cultural and reflective experience centred on Balinese purification and blessing traditions beside the river.'],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sort_order' => 6,
        ]);

        $spaSectionId = $this->updateSection($blogId, 'Experiencing the River During a Stay at Nandini', [
            'section_key' => 'split_media_section',
            'title' => 'SPA ON THE RIVER',
            'subtitle' => 'RIVERSIDE WELLNESS',
            'description' => '<p>Nandini\'s Spa on the River is a 180-minute riverside wellness experience beside the Ayung. The current experience begins with a soothing foot bath and continues with Nandini\'s signature treatment in the jungle setting.</p><p>This is a secondary, slower way to experience the riverside. Treatment details and enquiries remain on the dedicated Essence Spa website.</p>',
            'button_label' => 'EXPLORE ESSENCE SPA',
            'button_link_type' => 'manual',
            'button_url' => 'https://spa.nandinibali.com/',
            'button_route' => null,
            'items' => $this->items('spa_on_river', true),
            'sort_order' => 7,
        ]);

        if ($spaSectionId && Schema::hasTable('blog_news_section_images')) {
            $this->storeSectionImage(
                $spaSectionId,
                '/images/holy-river/SPA ON THE RIVER.webp',
                'Spa on the River treatment deck beside the Ayung River at Nandini Jungle',
            );
        }

        $this->updateSection($blogId, 'Best Time to Experience the Ayung River', [
            'section_key' => 'intro_text_section',
            'title' => 'BEST TIME TO EXPERIENCE THE AYUNG RIVER',
            'subtitle' => 'RIVER CONDITIONS',
            'description' => '<p>The Ayung can be experienced at different times of year, but conditions are not fixed. Rainfall can influence water level, current and the way rafting operators manage a route. Always follow current operator guidance rather than relying on a seasonal guarantee.</p><p>Quieter riverside experiences have different considerations from rafting and may depend on weather, access and availability. Nandini\'s team can advise on the latest arrangements for Holy River and Spa on the River experiences.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'items' => $this->items('river_conditions'),
            'sort_order' => 8,
        ]);

        $this->updateSection($blogId, 'How to Plan Your Ayung River Experience in Ubud', [
            'section_key' => 'intro_text_section',
            'title' => 'PLANNING AN AYUNG RIVER EXPERIENCE',
            'subtitle' => 'PLAN YOUR VISIT',
            'description' => '<p>Start with the type of river experience you want, then check the latest conditions and practical guidance with the relevant operator or Nandini team.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'background_color' => 'soft_gray',
            'items' => json_encode([
                'gtm_section' => 'planning',
                'presentation' => 'blocks',
                'entries' => [
                    ['title' => 'RAFTING CONDITIONS', 'description' => 'River conditions can vary. Follow the operator\'s current route, equipment and safety guidance.'],
                    ['title' => 'CHOOSING YOUR EXPERIENCE', 'description' => 'Choose rafting for active adventure, Holy River for cultural purification and reflection, or Spa on the River for riverside wellness.'],
                    ['title' => 'LOCATION', 'description' => 'Nandini is in Payangan, within the greater Ubud area, rather than central Ubud town. Allow for local travel time when planning.'],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sort_order' => 9,
        ]);

        $this->updateSection($blogId, 'Experience the Ayung River at Nandini Jungle by Hanging Gardens', [
            'section_key' => 'intro_text_section',
            'title' => 'EXPERIENCE THE AYUNG RIVER AT NANDINI',
            'subtitle' => 'THE AYUNG RIVER AT NANDINI',
            'description' => '<p>From white-water rafting to Balinese purification and quiet riverside wellness, the Ayung River forms an important part of the experience at Nandini Jungle by Hanging Gardens in Payangan, within the greater Ubud area.</p><p>Use the dedicated pages below to explore the experience that fits your visit or to plan a stay in the surrounding jungle landscape.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'background_color' => 'warm_ivory',
            'items' => json_encode([
                'gtm_section' => 'article_cta',
                'presentation' => 'links',
                'entries' => [
                    ['label' => 'AYUNG RIVER RAFTING', 'url' => 'https://nandinibali.com/experience/ayung-white-water-rafting'],
                    ['label' => 'HOLY RIVER', 'url' => 'https://nandinibali.com/holy-river'],
                    ['label' => 'ESSENCE SPA', 'url' => 'https://spa.nandinibali.com/'],
                    ['label' => 'STAY AT NANDINI', 'url' => 'https://nandinibali.com/ubud-jungle-resort-in-bali'],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'sort_order' => 10,
        ]);

        $this->updateFirstImageAlt($blogId, 'WHAT MAKES THE AYUNG RIVER SPECIAL?', 'White-water rafting on the Ayung River through Bali\'s tropical valley landscape');
        $this->updateFirstImageAlt($blogId, 'AYUNG RIVER FOR MELUKAT & BALINESE PURIFICATION', 'Jungle river and waterfall setting beside the Ayung River at Nandini Jungle');
        $this->updateFirstImageAlt($blogId, 'WHERE IS THE AYUNG RIVER IN BALI?', 'White-water rafting on the Ayung River in Bali');
    }

    public function down(): void
    {
        // Editorial content migrations do not restore superseded SEO copy or claims.
    }

    private function items(string $gtmSection, bool $preserveButtonLabel = false): string
    {
        return json_encode(array_filter([
            'gtm_section' => $gtmSection,
            'preserve_button_label' => $preserveButtonLabel ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function updateSection(int $blogId, string $currentTitle, array $values): ?int
    {
        $sectionId = DB::table('blog_news_sections')
            ->where('blog_news_id', $blogId)
            ->where('title', $currentTitle)
            ->value('id');

        if (! $sectionId) {
            return null;
        }

        DB::table('blog_news_sections')->where('id', $sectionId)->update(array_merge($values, [
            'updated_at' => now(),
        ]));

        return (int) $sectionId;
    }

    private function storeSectionImage(int $sectionId, string $image, string $alt): void
    {
        $imageId = DB::table('blog_news_section_images')
            ->where('blog_news_section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->value('id');

        $values = [
            'image' => $image,
            'image_file_name' => pathinfo($image, PATHINFO_FILENAME),
            'image_alt' => $alt,
            'mobile_image' => null,
            'mobile_image_file_name' => null,
            'mobile_image_alt' => null,
            'caption' => null,
            'is_active' => true,
            'sort_order' => 0,
            'updated_at' => now(),
        ];

        if ($imageId) {
            DB::table('blog_news_section_images')->where('id', $imageId)->update($values);

            return;
        }

        DB::table('blog_news_section_images')->insert(array_merge($values, [
            'id' => ((int) DB::table('blog_news_section_images')->max('id')) + 1,
            'blog_news_section_id' => $sectionId,
            'created_at' => now(),
        ]));
    }

    private function updateFirstImageAlt(int $blogId, string $sectionTitle, string $alt): void
    {
        if (! Schema::hasTable('blog_news_section_images')) {
            return;
        }

        $sectionId = DB::table('blog_news_sections')
            ->where('blog_news_id', $blogId)
            ->where('title', $sectionTitle)
            ->value('id');

        if (! $sectionId) {
            return;
        }

        DB::table('blog_news_section_images')
            ->where('blog_news_section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(1)
            ->update([
                'image_alt' => $alt,
                'mobile_image_alt' => $alt,
                'updated_at' => now(),
            ]);
    }
};
