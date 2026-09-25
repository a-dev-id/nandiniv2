<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const IMAGE_URL = 'https://nandinibali.com/storage/images/blog/attachment/jGWBRtzYE4r3WceXgxiXXylUJZNXOq9bJcIbHgAP.jpg';

    public function up(): void
    {
        if (! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_images')) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('section_key', 'about_story_gallery')
            ->first();

        if (! $section) {
            return;
        }

        $items = collect(json_decode($section->items ?: '[]', true) ?: []);

        if (! $items->contains(fn (array $item): bool => ($item['eyebrow'] ?? null) === 'Fitness Centre')) {
            $items->push([
                'eyebrow' => 'Fitness Centre',
                'title' => 'Wellness in Motion',
            ]);

            DB::table('page_sections')
                ->where('id', $section->id)
                ->update([
                    'items' => json_encode($items->values()->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
        }

        $imageExists = DB::table('page_section_images')
            ->where('page_section_id', $section->id)
            ->where('image', self::IMAGE_URL)
            ->exists();

        if (! $imageExists) {
            DB::table('page_section_images')->insert([
                'page_section_id' => $section->id,
                'image' => self::IMAGE_URL,
                'image_file_name' => null,
                'image_alt' => 'Gym and fitness centre at Nandini Jungle by Hanging Gardens',
                'mobile_image' => null,
                'mobile_image_file_name' => null,
                'mobile_image_alt' => null,
                'caption' => null,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('page_sections') || ! Schema::hasTable('page_section_images')) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('section_key', 'about_story_gallery')
            ->first();

        if (! $section) {
            return;
        }

        $items = collect(json_decode($section->items ?: '[]', true) ?: [])
            ->reject(fn (array $item): bool => ($item['eyebrow'] ?? null) === 'Fitness Centre')
            ->values()
            ->all();

        DB::table('page_sections')
            ->where('id', $section->id)
            ->update([
                'items' => json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);

        DB::table('page_section_images')
            ->where('page_section_id', $section->id)
            ->where('image', self::IMAGE_URL)
            ->delete();
    }
};
