<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SECTION_KEYS = [
        'honeymoon_hero',
        'honeymoon_intro',
        'honeymoon_features',
        'honeymoon_accommodations',
        'honeymoon_package',
        'honeymoon_dining',
        'honeymoon_spa',
        'honeymoon_itinerary',
        'honeymoon_celebrations',
        'honeymoon_faq',
        'honeymoon_final_cta',
    ];

    public function up(): void
    {
        $pageId = DB::table('pages')
            ->where(function ($query): void {
                $query
                    ->where('page_name', 'Honeymoon Page')
                    ->orWhereIn('slug', ['honeymoon', 'honeymoon-bali-packages']);
            })
            ->orderByRaw("CASE WHEN page_name = 'Honeymoon Page' THEN 0 ELSE 1 END")
            ->value('id');

        if (! $pageId) {
            return;
        }

        $nextSectionId = ((int) DB::table('page_sections')->max('id')) + 1;
        $nextImageId = ((int) DB::table('page_section_images')->max('id')) + 1;

        foreach ($this->sections() as $section) {
            if (DB::table('page_sections')
                ->where('page_id', $pageId)
                ->where('section_key', $section['section_key'])
                ->exists()) {
                continue;
            }

            $image = $section['image'] ?? null;
            unset($section['image']);

            $sectionId = $nextSectionId++;

            DB::table('page_sections')->insert(array_merge([
                'id' => $sectionId,
                'page_id' => $pageId,
                'button_link_type' => 'manual',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], $section, [
                'items' => isset($section['items'])
                    ? json_encode($section['items'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    : null,
            ]));

            if ($image) {
                DB::table('page_section_images')->insert([
                    'id' => $nextImageId++,
                    'page_section_id' => $sectionId,
                    'image' => $image['path'],
                    'image_alt' => $image['alt'],
                    'is_active' => true,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $sectionIds = DB::table('page_sections')
            ->whereIn('section_key', self::SECTION_KEYS)
            ->pluck('id');

        DB::table('page_section_images')->whereIn('page_section_id', $sectionIds)->delete();
        DB::table('page_sections')->whereIn('id', $sectionIds)->delete();
    }

    private function sections(): array
    {
        return [
            [
                'section_key' => 'honeymoon_hero',
                'subtitle' => 'Honeymoon',
                'title' => 'Honeymoon Resort in Ubud, Bali',
                'description' => '<p>A Romantic Jungle Honeymoon at Nandini Jungle by Hanging Gardens</p>',
                'sort_order' => 10,
                'image' => [
                    'path' => 'pages/hero/1c101d0a-da5a-4f26-9ab6-745ce9820373.webp',
                    'alt' => 'Honeymoon at Nandini Jungle by Hanging Gardens in Ubud, Bali',
                ],
            ],
            [
                'section_key' => 'honeymoon_intro',
                'subtitle' => 'A Honeymoon in Nature',
                'title' => 'Celebrate Your Honeymoon Surrounded by the Rainforest',
                'description' => '<p>Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Payangan, within the greater Ubud area of Bali. Set above the Ayung River valley, Nandini offers private jungle villas and Royal Suites, couples spa experiences, romantic dining and memorable moments designed for two.</p><p>Whether you are planning a Bali honeymoon, anniversary or romantic escape, the resort offers a peaceful setting where you can slow down, reconnect and experience Ubud together.</p>',
                'button_label' => 'Explore Our Resort',
                'button_url' => '#why',
                'sort_order' => 20,
                'image' => [
                    'path' => 'pages/sections/422be0cf-6a86-4d31-b124-a13e1c02880a.webp',
                    'alt' => 'Honeymoon experience at Nandini Jungle by Hanging Gardens in Ubud, Bali',
                ],
            ],
            [
                'section_key' => 'honeymoon_features',
                'subtitle' => 'Why Choose Nandini',
                'title' => 'Why Choose Nandini for Your Honeymoon in Ubud?',
                'description' => '<p>A honeymoon at Nandini is shaped by privacy, nature and time together. The resort sits along a tropical hillside overlooking the Ayung River valley, away from Bali\'s busier coastal areas while remaining within the greater Ubud region. Couples can stay in private jungle accommodation, unwind with spa and wellness experiences, enjoy romantic dining surrounded by nature and discover cultural and riverside experiences together.</p>',
                'items' => [
                    ['icon' => 'home', 'title' => 'Rainforest Setting', 'description' => 'Overlooking the Ayung River valley.'],
                    ['icon' => 'diamond', 'title' => 'Private Villas & Suites', 'description' => 'Designed for privacy, comfort and special moments.'],
                    ['icon' => 'sparkles', 'title' => 'Couples Spa & Wellness', 'description' => 'Relaxing treatments in the jungle.'],
                    ['icon' => 'heart', 'title' => 'Romantic Dining', 'description' => 'Intimate dining experiences for two.'],
                    ['icon' => 'leaf', 'title' => 'Unique Experiences', 'description' => 'Holy River, culture and nature activities.'],
                    ['icon' => 'star', 'title' => 'Perfect for Occasions', 'description' => 'Honeymoons, anniversaries and proposals.'],
                ],
                'sort_order' => 30,
            ],
            [
                'section_key' => 'honeymoon_accommodations',
                'subtitle' => 'Accommodation for Couples',
                'title' => 'Jungle Villas & Royal Suites for Your Honeymoon',
                'description' => '<p>From private jungle villas to spacious Royal Suites, each accommodation at Nandini is designed to offer privacy, comfort and a deeper connection with nature — perfect for a romantic stay in Ubud.</p>',
                'button_label' => 'Explore Villas & Royal Suites',
                'button_url' => '/the-royal-suites-and-jungle-villas',
                'items' => [
                    ['title' => 'Panoramic Jungle View Villa', 'description' => 'The Panoramic Jungle View Villa is ideal for couples who want a deeper sense of privacy and connection with nature. Its elevated setting and wide jungle views create a peaceful atmosphere for honeymoon mornings, quiet afternoons and relaxed evenings together.', 'image' => 'accommodations/cards/panoramic-jungle-view-villa-private-balcony-ubud-bali.webp', 'image_alt' => 'Panoramic Jungle View Villa at Nandini Jungle in Ubud', 'url' => '/jungle-villas/panoramic-jungle-view-villa', 'link_label' => 'View Details'],
                    ['title' => 'Private Garden Royal Suite', 'description' => 'More space and privacy for a romantic escape.', 'image' => 'accommodations/cards/private-garden-royal-suite-living-area-garden-view-ubud-bali.webp', 'image_alt' => 'Private Garden Royal Suite at Nandini Jungle', 'url' => '/the-royal-suites/private-garden-royal-suite', 'link_label' => 'View Details'],
                    ['title' => 'Panoramic Corner Jacuzzi Royal Suite', 'description' => 'Ideal for honeymoons and special celebrations.', 'image' => 'accommodations/cards/panoramic-corner-jacuzzi-royal-suite-balcony-jacuzzi-ubud-bali-2.webp', 'image_alt' => 'Panoramic Corner Jacuzzi Royal Suite at Nandini Jungle', 'url' => '/the-royal-suites/panoramic-corner-jacuzzi-royal-suite', 'link_label' => 'View Details'],
                ],
                'sort_order' => 40,
            ],
            [
                'section_key' => 'honeymoon_package',
                'subtitle' => 'Special Offer',
                'title' => '4 Days / 3 Nights Honeymoon Package',
                'description' => '<p>Created for couples celebrating a honeymoon or romantic escape, our 4 Days / 3 Nights honeymoon experience brings together time to relax, reconnect and enjoy Nandini\'s intimate jungle setting.</p>',
                'items' => [
                    ['label' => 'View Honeymoon Package', 'url' => '/honeymoon/honeymoon-packages-4-days-3-nights', 'style' => 'solid'],
                    ['label' => 'Reserve Your Honeymoon', 'url' => 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance', 'style' => 'outline'],
                ],
                'sort_order' => 50,
                'image' => [
                    'path' => 'pages/sections/422be0cf-6a86-4d31-b124-a13e1c02880a.webp',
                    'alt' => '4 Days 3 Nights honeymoon package at Nandini Jungle by Hanging Gardens',
                ],
            ],
            [
                'section_key' => 'honeymoon_dining',
                'subtitle' => 'Romantic Dining',
                'title' => 'Romantic Dining in the Jungle',
                'description' => '<p>Celebrate an evening together with romantic dining surrounded by Nandini\'s tropical landscape. From intimate dinners to special settings created for honeymoons, anniversaries and proposals, dining can become one of the most memorable moments of your stay.</p>',
                'items' => [
                    ['label' => 'Explore Dining', 'url' => 'https://dining.nandinibali.com/', 'style' => 'solid'],
                    ['label' => 'Romantic Experiences', 'url' => '/experiences/jungle-romance', 'style' => 'outline'],
                ],
                'sort_order' => 60,
                'image' => [
                    'path' => 'experience-categories/c5e0deb3-cd14-4488-ba06-31efb046d0fd.webp',
                    'alt' => 'Romantic jungle dining experience for couples at Nandini Jungle by Hanging Gardens',
                ],
            ],
            [
                'section_key' => 'honeymoon_spa',
                'subtitle' => 'Spa & Wellness',
                'title' => 'Spa & Wellness for Two',
                'description' => '<p>Slow down together with spa and wellness experiences inspired by Nandini\'s rainforest setting. Couples can enjoy relaxing treatments, riverside wellness experiences and quiet time surrounded by nature as part of their honeymoon in Ubud.</p>',
                'items' => [
                    ['label' => 'Explore Spa & Wellness', 'url' => '/spa-wellness', 'style' => 'solid'],
                    ['label' => 'Jungle Spa Ubud', 'url' => '/jungle-spa-ubud', 'style' => 'outline'],
                    ['label' => 'Holy River', 'url' => '/holy-river', 'style' => 'outline'],
                ],
                'sort_order' => 70,
                'image' => [
                    'path' => 'experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp',
                    'alt' => 'Couples spa and wellness experience at Nandini Jungle by Hanging Gardens',
                ],
            ],
            [
                'section_key' => 'honeymoon_itinerary',
                'subtitle' => 'Example Itinerary',
                'title' => 'A Suggested 4-Day Honeymoon in Ubud',
                'description' => '<p>An example itinerary to inspire your stay. It can be adjusted around your interests, preferred pace and the experiences you would like to include.</p>',
                'items' => [
                    ['label' => 'Day 1', 'title' => 'Arrive & Slow Down', 'description' => 'Check in, settle into your villa or suite and enjoy a relaxed evening together.', 'image' => 'accommodations/cards/panoramic-jungle-view-villa-private-balcony-ubud-bali.webp', 'image_alt' => 'Jungle villa in Ubud for honeymoon couples'],
                    ['label' => 'Day 2', 'title' => 'Spa & Romantic Dining', 'description' => 'Enjoy a couples wellness experience, followed by an intimate dinner.', 'image' => 'experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp', 'image_alt' => 'Spa and wellness experience at Nandini Jungle'],
                    ['label' => 'Day 3', 'title' => 'Experience Bali Together', 'description' => 'Explore a Holy River experience, village activity or another curated experience.', 'image' => 'pages/sections/16cc904d-d6b3-4050-959d-82884d7d4268.webp', 'image_alt' => 'Riverside wellness experience at Nandini Jungle'],
                    ['label' => 'Day 4', 'title' => 'A Slow Morning', 'description' => 'Enjoy breakfast and your final morning surrounded by the rainforest before departure.', 'image' => 'offers/cards/jungle-hideaway-dining-nandini-bali-2.webp', 'image_alt' => 'Romantic jungle dining at Nandini Jungle'],
                ],
                'sort_order' => 80,
            ],
            [
                'section_key' => 'honeymoon_celebrations',
                'subtitle' => 'Special Celebrations',
                'title' => 'Proposals, Anniversaries & Romantic Celebrations',
                'description' => '<p>Nandini is not only for honeymoons. Couples can also celebrate proposals, anniversaries and other meaningful occasions with romantic dining, spa experiences and personalized moments in the jungle.</p>',
                'button_label' => 'Plan a Romantic Celebration',
                'button_url' => 'https://dining.nandinibali.com/',
                'sort_order' => 90,
                'image' => [
                    'path' => 'offers/cards/jungle-hideaway-dining-nandini-bali-2.webp',
                    'alt' => 'Romantic proposal and anniversary celebration at Nandini Jungle by Hanging Gardens',
                ],
            ],
            [
                'section_key' => 'honeymoon_faq',
                'subtitle' => 'Frequently Asked Questions',
                'title' => 'Honeymoon in Ubud — Frequently Asked Questions',
                'items' => [
                    ['question' => 'Is Nandini Jungle by Hanging Gardens suitable for a honeymoon in Bali?', 'answer' => 'Yes. Nandini offers a secluded rainforest setting, private villas and Royal Suites, spa and wellness experiences, romantic dining and curated activities for couples.'],
                    ['question' => 'Where is Nandini located in relation to Ubud?', 'answer' => 'Nandini Jungle by Hanging Gardens is in Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area of Bali.'],
                    ['question' => 'Which villa or suite is best for honeymoon couples?', 'answer' => 'Couples can consider the Panoramic Jungle View Villa, Private Garden Royal Suite or Panoramic Corner Jacuzzi Royal Suite depending on their preferred level of space, privacy and atmosphere.'],
                    ['question' => 'Does Nandini offer a honeymoon package?', 'answer' => 'Yes. The honeymoon page features a 4 Days / 3 Nights Honeymoon Package. Visit the package detail page for the latest inclusions and booking information.'],
                    ['question' => 'Can couples arrange romantic dining?', 'answer' => 'Yes. Nandini offers romantic and private dining experiences for couples, including experiences suited to honeymoons, anniversaries and proposals.'],
                    ['question' => 'Does Nandini offer couples spa experiences?', 'answer' => 'Nandini offers spa and wellness experiences in its rainforest setting, including experiences suitable for couples seeking time to relax together.'],
                    ['question' => 'Can Nandini help with proposals or anniversary celebrations?', 'answer' => 'Romantic and private dining experiences are available by arrangement. Contact the Nandini team to discuss the preferred occasion and date.'],
                    ['question' => 'What activities can honeymoon couples experience in Ubud?', 'answer' => 'Couples can explore Nandini\'s curated experiences, including romantic dining, wellness, Holy River experiences and other nature and cultural activities.'],
                    ['question' => 'How many nights should couples stay for a honeymoon at Nandini?', 'answer' => 'The ideal stay depends on your plans. Nandini\'s featured honeymoon package is designed around a 4 Days / 3 Nights stay.'],
                    ['question' => 'How can we reserve our honeymoon stay?', 'answer' => 'You can reserve directly through Nandini\'s official booking engine or contact the reservations team for assistance.'],
                ],
                'sort_order' => 100,
            ],
            [
                'section_key' => 'honeymoon_final_cta',
                'subtitle' => 'Your Honeymoon Awaits',
                'title' => 'Plan Your Honeymoon in the Ubud Jungle',
                'description' => '<p>Celebrate your honeymoon surrounded by rainforest, river valley views and the quiet atmosphere of Nandini Jungle by Hanging Gardens. Explore our villas, Royal Suites and romantic experiences, or reserve your stay directly.</p>',
                'items' => [
                    ['label' => 'Explore Honeymoon Package', 'url' => '/honeymoon/honeymoon-packages-4-days-3-nights', 'style' => 'solid'],
                    ['label' => 'Reserve Your Stay', 'url' => 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance', 'style' => 'white-outline'],
                ],
                'sort_order' => 110,
                'image' => [
                    'path' => 'pages/hero/1c101d0a-da5a-4f26-9ab6-745ce9820373.webp',
                    'alt' => 'Romantic honeymoon escape at Nandini Jungle by Hanging Gardens in Ubud',
                ],
            ],
        ];
    }
};
