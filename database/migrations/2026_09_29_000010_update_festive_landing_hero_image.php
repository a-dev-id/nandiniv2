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

        DB::table('festive_settings')->update([
            'hero_image' => '/images/festive/2026/header-landing.jpg',
            'hero_image_alt' => 'Guest overlooking the tropical jungle from a private pool at Nandini Jungle',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('festive_settings')) {
            return;
        }

        DB::table('festive_settings')->update([
            'hero_image' => '/images/festive/2026/new-year-dining.jpg',
            'hero_image_alt' => 'Festive dining at Nandini Jungle',
            'updated_at' => now(),
        ]);
    }
};
