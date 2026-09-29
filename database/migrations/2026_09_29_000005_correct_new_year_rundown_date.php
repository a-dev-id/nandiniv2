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
                    if (($day['date'] ?? null) !== '31st of December 2025') {
                        continue;
                    }

                    $day['date'] = '31 December 2026';

                    $items = $day['items'] ?? [];

                    foreach ($items as &$item) {
                        if (($item['activity'] ?? null) === "GM's Speech & Countdown to 2026") {
                            $item['activity'] = "GM's Speech & Countdown to 2027";
                        }
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
                    if (($item['activity'] ?? null) === "GM's Speech & Countdown to 2026") {
                        $item['activity'] = "GM's Speech & Countdown to 2027";
                    }
                }

                DB::table('festive_events')->where('id', $event->id)->update([
                    'programme_heading' => "Programme of the Evening\n31 December 2026",
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
                    if (($day['date'] ?? null) !== '31 December 2026') {
                        continue;
                    }

                    $day['date'] = '31st of December 2025';

                    $items = $day['items'] ?? [];

                    foreach ($items as &$item) {
                        if (($item['activity'] ?? null) === "GM's Speech & Countdown to 2027") {
                            $item['activity'] = "GM's Speech & Countdown to 2026";
                        }
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
                    if (($item['activity'] ?? null) === "GM's Speech & Countdown to 2027") {
                        $item['activity'] = "GM's Speech & Countdown to 2026";
                    }
                }

                DB::table('festive_events')->where('id', $event->id)->update([
                    'programme_heading' => "Programme of the Evening\n31st of December 2025",
                    'programme_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
