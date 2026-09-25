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

        $this->updateSection('about_story_hero', [
            'description' => '<p>What began as an intimate hillside retreat has evolved over two decades into Nandini Jungle by Hanging Gardens — while remaining deeply connected to the landscape, community and traditions that shaped its beginning.</p>',
        ]);

        $this->updateSection('about_story_timeline', [
            'items' => [
                ['year' => 'Late 1990s', 'title' => 'The Beginning', 'description' => 'A chance encounter during Magnus Falk’s travels through the Ubud region leads to the land that would become Nandini.'],
                ['year' => '2005', 'title' => 'Nandini Opens', 'description' => 'Nandini opens as an intimate hillside retreat.'],
                ['year' => '2007', 'title' => 'The River', 'description' => 'Access through the jungle is created to reach the secluded riverside below the resort.'],
                ['year' => '2019', 'title' => 'A New Chapter', 'description' => 'Nandini thoughtfully expands while preserving the character of the original hillside retreat.'],
                ['year' => '2022', 'title' => 'Renewing Nandini', 'description' => 'Major renovation and upgrades refresh villas and resort facilities.'],
                ['year' => '2024', 'title' => 'New & Upgraded', 'description' => 'Royal Suites and new dining and wellness experiences mark another evolution.'],
                ['year' => 'Today', 'title' => 'The Story Continues', 'description' => 'Nandini continues to evolve in harmony with the jungle.'],
            ],
        ]);

        $this->updateSection('about_story_chapter', [
            'title' => "The Beginning.\nOne Remarkable Hillside.",
            'description' => '<p>Nandini officially opened in 2005 as an intimate retreat built along the steep jungle hillside. Inspired by traditional Balinese architecture, the original villas were created to sit naturally within their surroundings.</p>',
            'items' => [],
        ]);

        $this->updateSection('about_story_growth', [
            'description' => '<p>The 2019 expansion marked one of the most significant chapters in Nandini’s history, introducing new accommodation while retaining the jungle setting and character of the original hillside retreat.</p>',
            'items' => [],
        ]);

        $today = DB::table('page_sections')
            ->where('section_key', 'about_story_today')
            ->first();

        if ($today) {
            $links = collect(json_decode($today->items ?: '[]', true) ?: [])
                ->reject(fn (array $item): bool => ($item['kind'] ?? null) === 'stat')
                ->values()
                ->all();

            $this->updateSection('about_story_today', ['items' => $links]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('page_sections')) {
            return;
        }

        $this->updateSection('about_story_hero', [
            'description' => '<p>What began as an intimate collection of 18 hillside villas has evolved over two decades into Nandini Jungle by Hanging Gardens — while remaining deeply connected to the landscape, community and traditions that shaped its beginning.</p>',
        ]);

        $this->updateSection('about_story_timeline', [
            'items' => [
                ['year' => 'Late 1990s', 'title' => 'The Beginning', 'description' => 'A chance encounter during Magnus Falk’s travels through the Ubud region leads to the land that would become Nandini.'],
                ['year' => '2005', 'title' => 'Nandini Opens', 'description' => 'Nandini opens with 18 hillside villas.'],
                ['year' => '2007', 'title' => 'The River', 'description' => 'Access through the jungle is created to reach the secluded riverside below the resort.'],
                ['year' => '2019', 'title' => 'A New Chapter', 'description' => 'Nandini expands from 18 to 34 accommodation units.'],
                ['year' => '2022', 'title' => 'Renewing Nandini', 'description' => 'Major renovation and upgrades refresh villas and resort facilities.'],
                ['year' => '2024', 'title' => 'New & Upgraded', 'description' => 'Royal Suites and new dining and wellness experiences mark another evolution.'],
                ['year' => 'Today', 'title' => '18 + 16', 'description' => '18 Jungle Villas and 16 Royal Suites — 34 keys in total.'],
            ],
        ]);

        $this->updateSection('about_story_chapter', [
            'title' => "18 Villas.\nOne Remarkable Hillside.",
            'description' => '<p>Nandini officially opened in 2005 with 18 villas built along the steep jungle hillside. Inspired by traditional Balinese architecture, the original villas were created to sit naturally within their surroundings.</p>',
            'items' => [['value' => '18', 'label' => 'Original Villas']],
        ]);

        $this->updateSection('about_story_growth', [
            'description' => '<p>The 2019 expansion marked one of the most significant chapters in Nandini’s history, increasing the resort from 18 to 34 accommodation units while retaining the jungle setting and character of the original hillside retreat.</p>',
            'items' => [
                ['value' => '18', 'label' => 'Original Villas'],
                ['value' => '16', 'label' => 'New Units'],
                ['value' => '34', 'label' => 'Total Keys'],
            ],
        ]);

        $today = DB::table('page_sections')
            ->where('section_key', 'about_story_today')
            ->first();

        if ($today) {
            $links = collect(json_decode($today->items ?: '[]', true) ?: [])
                ->reject(fn (array $item): bool => ($item['kind'] ?? null) === 'stat')
                ->values()
                ->all();

            $this->updateSection('about_story_today', [
                'items' => array_merge([
                    ['kind' => 'stat', 'value' => '18', 'label' => 'Jungle Villas'],
                    ['kind' => 'stat', 'value' => '16', 'label' => 'Royal Suites'],
                    ['kind' => 'stat', 'value' => '34', 'label' => 'Total Keys'],
                ], $links),
            ]);
        }
    }

    private function updateSection(string $sectionKey, array $values): void
    {
        if (array_key_exists('items', $values)) {
            $values['items'] = json_encode($values['items'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $values['updated_at'] = now();

        DB::table('page_sections')
            ->where('section_key', $sectionKey)
            ->update($values);
    }
};
