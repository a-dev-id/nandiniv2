<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $rundown = [
            ['time' => '07:00 PM – 07:15 PM', 'activity' => 'Cocktail & Canape Soiree'],
            ['time' => '07:15 PM – 08:00 PM', 'activity' => 'Dinner with Balinese Dance Performance'],
            ['time' => '08:00 PM – 08:10 PM', 'activity' => "GM's Speech"],
            ['time' => '08:10 PM – 10:00 PM', 'activity' => 'Balinese Dance Performance'],
            ['time' => '10:00 PM – 11:45 PM', 'activity' => 'Cocktails & Social Party'],
            ['time' => '11:00 PM – 12:00 PM', 'activity' => "GM's Speech & Countdown to 2027"],
        ];

        if (Schema::hasTable('festive_settings')) {
            $settings = DB::table('festive_settings')->first();

            if ($settings) {
                $days = json_decode((string) $settings->programme_days, true) ?: [];
                $days = array_values(array_filter(
                    $days,
                    fn (array $day): bool => ! in_array(($day['date'] ?? null), ['31st of December 2025', '31 December 2026'], true)
                ));
                $days[] = [
                    'date' => '31 December 2026',
                    'items' => $rundown,
                ];

                DB::table('festive_settings')->where('id', $settings->id)->update([
                    'programme_heading' => 'Festive Programme at Nandini Jungle',
                    'programme_days' => json_encode($days, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('festive_events')) {
            DB::table('festive_events')->where('slug', 'new-year-dinner')->update([
                'programme_visible' => true,
                'programme_image' => '/images/festive/2026/new-year-dining.jpg',
                'programme_image_alt' => 'New Year Eve dinner at Nandini Jungle',
                'programme_eyebrow' => 'NEW YEAR EVE DINNER',
                'programme_heading' => "Programme of the Evening\n31 December 2026",
                'programme_items' => json_encode($rundown, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('festive_settings')) {
            $settings = DB::table('festive_settings')->first();

            if ($settings) {
                $days = json_decode((string) $settings->programme_days, true) ?: [];
                $days = array_values(array_filter(
                    $days,
                    fn (array $day): bool => ! in_array(($day['date'] ?? null), ['31st of December 2025', '31 December 2026'], true)
                ));

                DB::table('festive_settings')->where('id', $settings->id)->update([
                    'programme_heading' => 'Christmas Experience at Nandini Jungle',
                    'programme_days' => json_encode($days, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('festive_events')) {
            DB::table('festive_events')->where('slug', 'new-year-dinner')->update([
                'programme_visible' => false,
                'programme_image' => null,
                'programme_image_alt' => null,
                'programme_eyebrow' => null,
                'programme_heading' => null,
                'programme_items' => json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }
    }
};
