<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('festive_settings')) {
            return;
        }

        $settings = DB::table('festive_settings')->first();

        if (! $settings) {
            return;
        }

        $celebrations = json_decode((string) $settings->celebrations, true) ?: [];

        foreach ($celebrations as &$celebration) {
            if (($celebration['anchor'] ?? null) === 'christmas') {
                $celebration['button_label'] = 'VIEW CHRISTMAS DINNER';
                $celebration['button_url'] = '/festive-season/christmas-dinner';
            }

            if (($celebration['anchor'] ?? null) === 'new-year') {
                $celebration['button_label'] = 'VIEW NEW YEAR DINNER';
                $celebration['button_url'] = '/festive-season/new-year-dinner';
            }
        }

        DB::table('festive_settings')->where('id', $settings->id)->update([
            'hero_primary_cta_url' => '/festive-season/christmas-dinner',
            'hero_secondary_cta_url' => '/festive-season/new-year-dinner',
            'celebrations' => json_encode($celebrations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('festive_settings')) {
            return;
        }

        $settings = DB::table('festive_settings')->first();

        if (! $settings) {
            return;
        }

        $celebrations = json_decode((string) $settings->celebrations, true) ?: [];

        foreach ($celebrations as &$celebration) {
            if (($celebration['anchor'] ?? null) === 'christmas') {
                $celebration['button_label'] = 'RESERVE CHRISTMAS DINNER';
                $celebration['button_url'] = 'https://wa.me/6281236871170';
            }

            if (($celebration['anchor'] ?? null) === 'new-year') {
                $celebration['button_label'] = 'RESERVE NEW YEAR DINNER';
                $celebration['button_url'] = 'https://wa.me/6281236871170';
            }
        }

        DB::table('festive_settings')->where('id', $settings->id)->update([
            'hero_primary_cta_url' => '#christmas',
            'hero_secondary_cta_url' => '#new-year',
            'celebrations' => json_encode($celebrations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }
};
