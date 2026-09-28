<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->string('hero_video_id')->nullable()->after('hero_visible');
        });

        DB::table('spa_settings')->update([
            'hero_video_id' => 'jafQbgUnfL4',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->dropColumn('hero_video_id');
        });
    }
};
