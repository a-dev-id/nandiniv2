<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const META_TITLE = 'Honeymoon Resort in Ubud, Bali | Nandini Jungle';

    private const META_DESCRIPTION = 'Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Ubud, Bali with private villas, spa, dining and couples experiences.';

    private const BOOKING_URL = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance';

    public function up(): void
    {
        $pageId = $this->honeymoonPageId();

        if (! $pageId) {
            return;
        }

        DB::table('pages')->where('id', $pageId)->update([
            'meta_title' => self::META_TITLE,
            'meta_description' => self::META_DESCRIPTION,
            'updated_at' => now(),
        ]);

        $this->updateSection($pageId, 'honeymoon_intro', [
            'description' => '<p>Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Payangan, within the greater Ubud area of Bali. Set above the Ayung River valley, Nandini offers private jungle villas and Royal Suites, couples spa experiences, romantic dining and memorable moments designed for two.</p><p>Whether you are planning a Bali honeymoon, anniversary or romantic escape, the resort offers a peaceful setting where you can slow down, reconnect and experience Ubud together.</p>',
        ]);

        $this->updateSection($pageId, 'honeymoon_features', [
            'description' => '<p>A honeymoon at Nandini is shaped by privacy, nature and time together. The resort sits along a tropical hillside overlooking the Ayung River valley, away from Bali\'s busier coastal areas while remaining within the greater Ubud region. Couples can stay in private jungle accommodation, unwind with spa and wellness experiences, enjoy romantic dining surrounded by nature and discover cultural and riverside experiences together.</p>',
        ]);

        $this->updateItems($pageId, 'honeymoon_accommodations', function (array $items): array {
            foreach ($items as &$item) {
                if (($item['title'] ?? null) === 'Panoramic Jungle View Villa') {
                    $item['description'] = 'The Panoramic Jungle View Villa is ideal for couples who want a deeper sense of privacy and connection with nature. Its elevated setting and wide jungle views create a peaceful atmosphere for honeymoon mornings, quiet afternoons and relaxed evenings together.';
                }
            }

            return $items;
        });

        foreach (['honeymoon_package', 'honeymoon_final_cta'] as $sectionKey) {
            $this->updateItems($pageId, $sectionKey, function (array $items): array {
                foreach ($items as &$item) {
                    if (in_array($item['label'] ?? null, ['Reserve Your Honeymoon', 'Reserve Your Stay'], true)) {
                        $item['url'] = self::BOOKING_URL;
                    }
                }

                return $items;
            });
        }

        DB::table('honeymoons')
            ->where('slug', 'honeymoon-packages-4-days-3-nights')
            ->update([
                'booking_url_override' => self::BOOKING_URL,
                'updated_at' => now(),
            ]);

        $this->updateImageAlt($pageId, 'honeymoon_intro', 'Honeymoon experience at Nandini Jungle by Hanging Gardens in Ubud, Bali');
        $this->updateImageAlt($pageId, 'honeymoon_package', '4 Days 3 Nights honeymoon package at Nandini Jungle by Hanging Gardens');
        $this->updateImageAlt($pageId, 'honeymoon_dining', 'Romantic jungle dining experience for couples at Nandini Jungle by Hanging Gardens');
        $this->updateImageAlt($pageId, 'honeymoon_spa', 'Couples spa and wellness experience at Nandini Jungle by Hanging Gardens');
        $this->updateImageAlt($pageId, 'honeymoon_celebrations', 'Romantic proposal and anniversary celebration at Nandini Jungle by Hanging Gardens');
        $this->updateImageAlt($pageId, 'honeymoon_final_cta', 'Romantic honeymoon escape at Nandini Jungle by Hanging Gardens in Ubud');
    }

    public function down(): void
    {
        $pageId = $this->honeymoonPageId();

        if (! $pageId) {
            return;
        }

        DB::table('honeymoons')
            ->where('slug', 'honeymoon-packages-4-days-3-nights')
            ->update([
                'booking_url_override' => 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance',
                'updated_at' => now(),
            ]);
    }

    private function honeymoonPageId(): ?int
    {
        $id = DB::table('pages')
            ->where(function ($query): void {
                $query
                    ->where('page_name', 'Honeymoon Page')
                    ->orWhereIn('slug', ['honeymoon', 'honeymoon-bali-packages']);
            })
            ->orderByRaw("CASE WHEN page_name = 'Honeymoon Page' THEN 0 ELSE 1 END")
            ->value('id');

        return $id ? (int) $id : null;
    }

    private function updateSection(int $pageId, string $sectionKey, array $values): void
    {
        DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('section_key', $sectionKey)
            ->update(array_merge($values, ['updated_at' => now()]));
    }

    private function updateItems(int $pageId, string $sectionKey, callable $callback): void
    {
        $section = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('section_key', $sectionKey)
            ->first(['id', 'items']);

        if (! $section) {
            return;
        }

        $items = json_decode((string) $section->items, true);
        $items = is_array($items) ? $callback($items) : [];

        DB::table('page_sections')->where('id', $section->id)->update([
            'items' => json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    private function updateImageAlt(int $pageId, string $sectionKey, string $alt): void
    {
        $sectionId = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('section_key', $sectionKey)
            ->value('id');

        if (! $sectionId) {
            return;
        }

        DB::table('page_section_images')
            ->where('page_section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(1)
            ->update([
                'image_alt' => $alt,
                'updated_at' => now(),
            ]);
    }
};
