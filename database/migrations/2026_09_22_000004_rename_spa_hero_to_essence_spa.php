<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('spa_settings') && Schema::hasColumn('spa_settings', 'hero_heading')) {
            DB::table('spa_settings')->where('id', 1)->update([
                'hero_heading' => 'Essence Spa',
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('spa_settings') && Schema::hasColumn('spa_settings', 'hero_heading')) {
            DB::table('spa_settings')->where('id', 1)->update([
                'hero_heading' => "Nandini\nJungle Spa",
                'updated_at' => now(),
            ]);
        }
    }
};
