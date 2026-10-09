<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PAGE_ID = 8;

    private const NEW_SECTION_KEYS = [
        'wedding_ceremony_options',
        'wedding_dining',
        'wedding_accommodation',
        'wedding_planning',
        'wedding_final_cta',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $page = DB::table('pages')->where('id', self::PAGE_ID)->first();

        if (! $page) {
            return;
        }

        $now = now();

        DB::table('pages')->where('id', self::PAGE_ID)->update([
            'title' => 'Jungle Wedding Venue in Ubud, Bali',
            'subtitle' => "Celebrate Your Story Surrounded by Bali's Jungle",
            'description' => '<p>Set within the tropical landscape of Payangan, in the greater Ubud area, Nandini Jungle by Hanging Gardens offers an intimate destination wedding setting surrounded by rainforest and the Ayung River valley. Couples can choose between a jungle chapel ceremony and a riverside celebration, with accommodation, dining and personalised wedding arrangements available within the resort.</p><p>Here, each celebration unfolds in the quiet rhythm of the jungle. Exchange vows framed by tropical greenery, gather with the people closest to you and continue your story with a stay surrounded by nature.</p>',
            'meta_title' => 'Jungle Wedding Venue in Ubud, Bali | Nandini Jungle',
            'meta_description' => 'Celebrate your wedding at Nandini Jungle, a jungle wedding venue in Ubud, Bali with a private chapel, Ayung River ceremony setting, dining and accommodation.',
            'hero_image_alt' => 'Destination wedding in the tropical jungle at Nandini Bali',
            'hero_mobile_image_alt' => 'Destination wedding in the tropical jungle at Nandini Bali',
            'updated_at' => $now,
        ]);

        $chapelId = DB::table('page_sections')
            ->where('page_id', self::PAGE_ID)
            ->where(function ($query): void {
                $query->where('id', 60)
                    ->orWhere('title', 'Wedding by the Chapel')
                    ->orWhere('title', 'WEDDING BY THE CHAPEL')
                    ->orWhere('section_key', 'wedding_chapel');
            })
            ->value('id');

        if ($chapelId) {
            DB::table('page_sections')->where('id', $chapelId)->update([
                'section_key' => 'wedding_chapel',
                'subtitle' => 'JUNGLE WEDDING CHAPEL',
                'title' => 'WEDDING BY THE CHAPEL',
                'description' => '<p>Set within Nandini\'s tropical landscape, the jungle chapel offers an intimate ceremony environment framed by natural surroundings and forest views. Fresh blooms and the chapel\'s quiet setting create an elegant place to exchange vows.</p><p>Couples can speak with the Nandini team about the details of their celebration and how the chapel setting can reflect their preferred ceremony style.</p>',
                'button_label' => null,
                'button_url' => null,
                'button_route' => null,
                'background_color' => 'white',
                'sort_order' => 2,
                'updated_at' => $now,
            ]);

            $this->updateImageAlt((int) $chapelId, 'Jungle wedding chapel at Nandini Jungle in Ubud', $now);
        }

        $riverId = DB::table('page_sections')
            ->where('page_id', self::PAGE_ID)
            ->where(function ($query): void {
                $query->where('id', 59)
                    ->orWhere('title', 'Wedding By the River')
                    ->orWhere('title', 'WEDDING BY THE RIVER')
                    ->orWhere('section_key', 'wedding_river');
            })
            ->value('id');

        if ($riverId) {
            DB::table('page_sections')->where('id', $riverId)->update([
                'section_key' => 'wedding_river',
                'subtitle' => 'AYUNG RIVER WEDDING',
                'title' => 'WEDDING BY THE RIVER',
                'description' => '<p>Beside the Ayung River in Payangan, within the greater Ubud area, Nandini offers a wedding setting surrounded by rainforest, green hills and the gentle movement of the river. Couples can exchange vows beneath the trees in an atmosphere shaped by nature.</p><p>After the ceremony, Nandini\'s culinary team can create a custom menu for the celebration, bringing the riverside occasion together through food, place and time shared with family and friends.</p>',
                'button_label' => null,
                'button_url' => null,
                'button_route' => null,
                'background_color' => 'soft_gray',
                'sort_order' => 3,
                'updated_at' => $now,
            ]);

            $this->updateImageAlt((int) $riverId, 'Wedding ceremony setting beside the Ayung River at Nandini Jungle', $now);
        }

        $sections = [
            [
                'section_key' => 'wedding_ceremony_options',
                'subtitle' => 'CEREMONY EXPERIENCES',
                'title' => 'YOUR CEREMONY, YOUR STORY',
                'description' => '<p>Choose a ceremony direction that feels true to your story, then speak with the Nandini team about the setting and arrangements for your day.</p>',
                'items' => [
                    [
                        'title' => 'BALINESE-INSPIRED CEREMONY',
                        'description' => 'A ceremony direction inspired by Balinese tradition and the natural atmosphere of Nandini Jungle.',
                    ],
                    [
                        'title' => 'CLASSIC WESTERN CEREMONY',
                        'description' => 'A classic ceremony style for couples who want to exchange vows in Nandini\'s chapel or riverside setting.',
                    ],
                    [
                        'title' => 'SIGNATURE ENCHANTING WEDDING',
                        'description' => 'Nandini\'s signature wedding concept, created for a celebration surrounded by the beauty of the jungle.',
                    ],
                ],
                'background_color' => 'white',
                'sort_order' => 4,
            ],
            [
                'section_key' => 'wedding_dining',
                'subtitle' => 'DINING & CELEBRATION',
                'title' => 'WEDDING DINING & CELEBRATIONS',
                'description' => '<p>Bring your celebration together around a menu created for the occasion. Nandini\'s culinary team can design a custom wedding menu, while the resort\'s romantic dining experiences offer further inspiration for time together before or after the wedding day.</p>',
                'button_label' => 'EXPLORE DINING',
                'button_url' => 'https://dining.nandinibali.com/',
                'background_color' => 'soft_gray',
                'sort_order' => 5,
                'image' => '/images/dining/romantic-dining-by-the-chapel.webp',
                'image_alt' => 'Romantic candlelit dining by the chapel at Nandini Jungle',
            ],
            [
                'section_key' => 'wedding_accommodation',
                'subtitle' => 'DESTINATION WEDDING STAY',
                'title' => 'STAY TOGETHER IN THE JUNGLE',
                'description' => '<p>Extend your wedding celebration into a destination stay at Nandini Jungle. The couple and their guests can explore private jungle villas and Royal Suites, with space to slow down and enjoy the rainforest setting before and after the ceremony.</p><p>For a romantic continuation after the celebration, discover a <a href="https://nandinibali.com/honeymoon">honeymoon at Nandini Jungle</a>.</p>',
                'items' => [
                    [
                        'title' => 'JUNGLE VILLAS',
                        'description' => 'Private villas surrounded by Nandini\'s tropical landscape.',
                        'url' => 'https://nandinibali.com/jungle-villas',
                        'link_label' => 'EXPLORE JUNGLE VILLAS',
                    ],
                    [
                        'title' => 'ROYAL SUITES',
                        'description' => 'Spacious suites for an elevated stay in the Ubud jungle.',
                        'url' => 'https://nandinibali.com/the-royal-suites',
                        'link_label' => 'EXPLORE ROYAL SUITES',
                    ],
                ],
                'background_color' => 'white',
                'sort_order' => 6,
            ],
            [
                'section_key' => 'wedding_planning',
                'subtitle' => 'WEDDING PLANNING',
                'title' => 'PLANNING YOUR WEDDING AT NANDINI',
                'description' => '<p>Begin with the setting and ceremony style that feel right for you. The Nandini team can then help you explore the wedding arrangements available within the resort.</p>',
                'items' => [
                    [
                        'title' => 'VENUE',
                        'description' => 'Choose between the intimate jungle chapel and a celebration beside the Ayung River.',
                    ],
                    [
                        'title' => 'CEREMONY',
                        'description' => 'Explore a Balinese-inspired ceremony, a classic Western ceremony or Nandini\'s Signature Enchanting Wedding.',
                    ],
                    [
                        'title' => 'DINING',
                        'description' => 'Discuss a custom wedding menu created by Nandini\'s culinary team for your celebration.',
                    ],
                    [
                        'title' => 'STAY',
                        'description' => 'Explore jungle villas and Royal Suites for the couple and guests joining the destination celebration.',
                    ],
                ],
                'background_color' => 'soft_gray',
                'sort_order' => 7,
            ],
            [
                'section_key' => 'wedding_final_cta',
                'subtitle' => 'YOUR CELEBRATION BEGINS HERE',
                'title' => 'BEGIN YOUR WEDDING JOURNEY',
                'description' => '<p>Tell us how you imagine your celebration, and our team will help you explore the most suitable venue, ceremony style and wedding arrangements at Nandini Jungle.</p>',
                'items' => [
                    [
                        'label' => 'PLAN YOUR WEDDING',
                        'url' => '#wedding-inquiry',
                        'style' => 'solid',
                    ],
                    [
                        'label' => 'WHATSAPP OUR TEAM',
                        'url' => 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20planning%20a%20wedding%20at%20Nandini%20Jungle%20by%20Hanging%20Gardens%20in%20Ubud%2C%20Bali.',
                        'style' => 'white-outline',
                    ],
                ],
                'background_color' => 'dark',
                'sort_order' => 8,
                'image' => $page->hero_image ?: $page->hero_mobile_image,
                'mobile_image' => $page->hero_mobile_image ?: $page->hero_image,
                'image_alt' => 'Destination wedding in the tropical jungle at Nandini Bali',
            ],
        ];

        foreach ($sections as $section) {
            $image = $section['image'] ?? null;
            $mobileImage = $section['mobile_image'] ?? null;
            $imageAlt = $section['image_alt'] ?? null;
            unset($section['image'], $section['mobile_image'], $section['image_alt']);

            $section['items'] = isset($section['items'])
                ? json_encode($section['items'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : null;

            $sectionId = DB::table('page_sections')
                ->where('page_id', self::PAGE_ID)
                ->where('section_key', $section['section_key'])
                ->value('id');

            $values = $section + [
                'page_id' => self::PAGE_ID,
                'button_link_type' => 'manual',
                'text_align' => 'center',
                'is_active' => true,
                'updated_at' => $now,
            ];

            if ($sectionId) {
                DB::table('page_sections')->where('id', $sectionId)->update($values);
            } else {
                $sectionId = ((int) DB::table('page_sections')->max('id')) + 1;
                DB::table('page_sections')->insert($values + [
                    'id' => $sectionId,
                    'created_at' => $now,
                ]);
            }

            if ($image && Schema::hasTable('page_section_images')) {
                $this->storeImage((int) $sectionId, $image, $mobileImage, $imageAlt, $now);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $newSectionIds = DB::table('page_sections')
            ->where('page_id', self::PAGE_ID)
            ->whereIn('section_key', self::NEW_SECTION_KEYS)
            ->pluck('id');

        if (Schema::hasTable('page_section_images') && $newSectionIds->isNotEmpty()) {
            DB::table('page_section_images')->whereIn('page_section_id', $newSectionIds)->delete();
        }

        DB::table('page_sections')->whereIn('id', $newSectionIds)->delete();

        DB::table('pages')->where('id', self::PAGE_ID)->update([
            'title' => 'Wedding',
            'subtitle' => null,
            'meta_title' => 'Wedding Venue in Ubud Bali | Nandini Jungle',
            'meta_description' => 'Celebrate your wedding at Nandini Jungle by Hanging Gardens, a romantic Ubud jungle resort with forest views, flowing water, and elegant ceremony settings.',
            'hero_image_alt' => 'Wedding Venue in Ubud Bali | Nandini Jungle',
            'hero_mobile_image_alt' => 'Wedding Venue in Ubud Bali | Nandini Jungle',
            'updated_at' => now(),
        ]);

        DB::table('page_sections')->where('page_id', self::PAGE_ID)->where('section_key', 'wedding_chapel')->update([
            'section_key' => 'split_media_reverse',
            'title' => 'Wedding by the Chapel',
            'subtitle' => null,
            'sort_order' => 2,
            'updated_at' => now(),
        ]);

        DB::table('page_sections')->where('page_id', self::PAGE_ID)->where('section_key', 'wedding_river')->update([
            'section_key' => 'split_media_section',
            'title' => 'Wedding By the River',
            'subtitle' => null,
            'sort_order' => 1,
            'updated_at' => now(),
        ]);
    }

    private function updateImageAlt(int $sectionId, string $alt, mixed $now): void
    {
        if (! Schema::hasTable('page_section_images')) {
            return;
        }

        $imageId = DB::table('page_section_images')
            ->where('page_section_id', $sectionId)
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if (! $imageId) {
            return;
        }

        DB::table('page_section_images')
            ->where('id', $imageId)
            ->update([
                'image_alt' => $alt,
                'mobile_image_alt' => $alt,
                'updated_at' => $now,
            ]);
    }

    private function storeImage(int $sectionId, string $image, ?string $mobileImage, ?string $alt, mixed $now): void
    {
        $imageId = DB::table('page_section_images')
            ->where('page_section_id', $sectionId)
            ->where('sort_order', 1)
            ->value('id');

        $values = [
            'page_section_id' => $sectionId,
            'image' => $image,
            'image_alt' => $alt,
            'mobile_image' => $mobileImage,
            'mobile_image_alt' => $alt,
            'is_active' => true,
            'sort_order' => 1,
            'updated_at' => $now,
        ];

        if ($imageId) {
            DB::table('page_section_images')->where('id', $imageId)->update($values);

            return;
        }

        DB::table('page_section_images')->insert($values + [
            'id' => ((int) DB::table('page_section_images')->max('id')) + 1,
            'created_at' => $now,
        ]);
    }
};
