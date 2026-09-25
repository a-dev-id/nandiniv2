<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceTitle('Contemporary Jungle Living', 'Jungle Living');
    }

    public function down(): void
    {
        $this->replaceTitle('Jungle Living', 'Contemporary Jungle Living');
    }

    private function replaceTitle(string $currentTitle, string $newTitle): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('section_key', 'about_story_gallery')
            ->first();

        if (! $section) {
            return;
        }

        $items = collect(json_decode($section->items ?: '[]', true) ?: [])
            ->map(function (array $item) use ($currentTitle, $newTitle): array {
                if (($item['title'] ?? null) === $currentTitle) {
                    $item['title'] = $newTitle;
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
