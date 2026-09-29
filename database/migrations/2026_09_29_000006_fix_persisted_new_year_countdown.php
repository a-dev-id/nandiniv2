<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('festive_settings')) {
            $settings = DB::table('festive_settings')->first();

            if ($settings) {
                $days = json_decode((string) $settings->programme_days, true) ?: [];

                foreach ($days as &$day) {
                    $items = $day['items'] ?? [];

                    foreach ($items as &$item) {
                        $activity = (string) ($item['activity'] ?? '');
                        $item['activity'] = str_replace('Countdown to 2026', 'Countdown to 2027', $activity);
                    }

                    $day['items'] = $items;
                }

                DB::table('festive_settings')->where('id', $settings->id)->update([
                    'programme_days' => json_encode($days, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('festive_events')) {
            $event = DB::table('festive_events')->where('slug', 'new-year-dinner')->first();

            if ($event) {
                $items = json_decode((string) $event->programme_items, true) ?: [];

                foreach ($items as &$item) {
                    $activity = (string) ($item['activity'] ?? '');
                    $item['activity'] = str_replace('Countdown to 2026', 'Countdown to 2027', $activity);
                }

                DB::table('festive_events')->where('id', $event->id)->update([
                    'programme_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('festive_settings')) {
            $settings = DB::table('festive_settings')->first();

            if ($settings) {
                $days = json_decode((string) $settings->programme_days, true) ?: [];

                foreach ($days as &$day) {
                    $items = $day['items'] ?? [];

                    foreach ($items as &$item) {
                        $activity = (string) ($item['activity'] ?? '');
                        $item['activity'] = str_replace('Countdown to 2027', 'Countdown to 2026', $activity);
                    }

                    $day['items'] = $items;
                }

                DB::table('festive_settings')->where('id', $settings->id)->update([
                    'programme_days' => json_encode($days, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('festive_events')) {
            $event = DB::table('festive_events')->where('slug', 'new-year-dinner')->first();

            if ($event) {
                $items = json_decode((string) $event->programme_items, true) ?: [];

                foreach ($items as &$item) {
                    $activity = (string) ($item['activity'] ?? '');
                    $item['activity'] = str_replace('Countdown to 2027', 'Countdown to 2026', $activity);
                }

                DB::table('festive_events')->where('id', $event->id)->update([
                    'programme_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
