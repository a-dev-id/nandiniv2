<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BOOKING_URL = 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20book%20the%20Spa%20on%20the%20River%20experience%20at%20Nandini%20Jungle.';

    public function up(): void
    {
        if (! Schema::hasTable('spa_settings')) {
            return;
        }

        DB::table('spa_settings')->where('id', 1)->update([
            'signature_link_label' => 'BOOK NOW',
            'signature_link_url' => self::BOOKING_URL,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('spa_settings')) {
            return;
        }

        DB::table('spa_settings')->where('id', 1)->update([
            'signature_link_label' => 'DISCOVER THE SPA',
            'signature_link_url' => 'https://'.config('domains.main').'/spa-wellness',
            'updated_at' => now(),
        ]);
    }
};
