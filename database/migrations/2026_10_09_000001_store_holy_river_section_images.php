<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_images')) {
            return;
        }

        $pageId = DB::table('pages')
            ->where(function ($query): void {
                $query
                    ->where('page_name', 'Holy River Page')
                    ->orWhere('slug', 'holy-river');
            })
            ->orderByRaw("CASE WHEN page_name = 'Holy River Page' THEN 0 ELSE 1 END")
            ->value('id');

        if (! $pageId) {
            return;
        }

        $this->storeSectionImage(
            (int) $pageId,
            'A SACRED SETTING BY THE AYUNG RIVER',
            '/images/holy-river/A-SACRED-SETTING-BY-THE-AYUNG-RIVER.jpg',
            'Sacred riverside deck surrounded by tropical jungle at Nandini Jungle',
        );

        $this->storeSectionImage(
            (int) $pageId,
            'BALINESE BLESSING & PURIFICATION',
            '/images/holy-river/BALINESE-BLESSING-&-PURIFICATION.jpg',
            'Balinese blessing and purification ceremony beside the Ayung River',
        );

        $this->storeSectionImage(
            (int) $pageId,
            'SPA ON THE RIVER',
            '/images/holy-river/SPA ON THE RIVER.webp',
            'Spa treatment beds beside the Ayung River at Nandini Jungle',
        );

        $bookingSectionId = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('section_key', 'holy_river_booking_cta')
            ->value('id');

        if (! $bookingSectionId) {
            $bookingSectionId = ((int) DB::table('page_sections')->max('id')) + 1;

            DB::table('page_sections')->insert([
                'id' => $bookingSectionId,
                'page_id' => $pageId,
                'section_key' => 'holy_river_booking_cta',
                'title' => 'PLAN YOUR HOLY RIVER EXPERIENCE',
                'subtitle' => 'HOLY RIVER AT NANDINI',
                'description' => '<p>Experience Balinese purification, traditional blessings and the peaceful setting of the Ayung River at Nandini Jungle.</p>',
                'button_label' => 'RESERVE',
                'button_link_type' => 'manual',
                'button_url' => 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20the%20Holy%20River%20and%20Balinese%20purification%20experiences%20at%20Nandini%20Jungle.',
                'text_align' => 'center',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->storeImage(
            (int) $bookingSectionId,
            '/images/holy-river/PLAN YOUR HOLY RIVER EXPERIENCE.webp',
            'Guest meditating beside the Holy River at Nandini Jungle',
        );
    }

    public function down(): void
    {
        // Content migrations do not remove administrator-managed media on rollback.
    }

    private function storeSectionImage(int $pageId, string $title, string $image, string $alt): void
    {
        $sectionId = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('title', $title)
            ->value('id');

        if ($sectionId) {
            $this->storeImage((int) $sectionId, $image, $alt);
        }
    }

    private function storeImage(int $sectionId, string $image, string $alt): void
    {
        $imageId = DB::table('page_section_images')
            ->where('page_section_id', $sectionId)
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
            'is_active' => true,
            'sort_order' => 0,
            'updated_at' => now(),
        ];

        if ($imageId) {
            DB::table('page_section_images')->where('id', $imageId)->update($values);

            return;
        }

        DB::table('page_section_images')->insert(array_merge($values, [
            'id' => ((int) DB::table('page_section_images')->max('id')) + 1,
            'page_section_id' => $sectionId,
            'created_at' => now(),
        ]));
    }
};
