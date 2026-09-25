<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('section_key', 'about_story_comparison')
            ->first();

        if (! $section) {
            return;
        }

        $items = collect(json_decode($section->items ?: '[]', true) ?: [])
            ->map(function (array $item): array {
                if (($item['label'] ?? null) === 'Then') {
                    $item['title'] = 'The Early Years';
                    $item['description'] = 'Nandini began as an intimate collection of hillside villas, shaped by the jungle and rooted in Balinese character.';
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

    public function down(): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('section_key', 'about_story_comparison')
            ->first();

        if (! $section) {
            return;
        }

        $items = collect(json_decode($section->items ?: '[]', true) ?: [])
            ->map(function (array $item): array {
                if (($item['label'] ?? null) === 'Then') {
                    $item['title'] = 'Historical Nandini Image Required';
                    $item['description'] = 'Add a genuine 2005–2010 image from Nandini’s archive when available. No fabricated history photography.';
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
