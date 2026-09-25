<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceTitles([
            'Discover Nandini Experiences' => 'Nandini Experiences',
            'View the Nandini Gallery' => 'Nandini Gallery',
        ]);
    }

    public function down(): void
    {
        $this->replaceTitles([
            'Nandini Experiences' => 'Discover Nandini Experiences',
            'Nandini Gallery' => 'View the Nandini Gallery',
        ]);
    }

    private function replaceTitles(array $replacements): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('section_key', 'about_story_today')
            ->first();

        if (! $section) {
            return;
        }

        $items = collect(json_decode($section->items ?: '[]', true) ?: [])
            ->map(function (array $item) use ($replacements): array {
                $title = $item['title'] ?? null;

                if (is_string($title) && isset($replacements[$title])) {
                    $item['title'] = $replacements[$title];
                }

                return $item;
            })
            ->all();

        DB::table('page_sections')
            ->where('id', $section->id)
            ->update([
                'items' => json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
    }
};
