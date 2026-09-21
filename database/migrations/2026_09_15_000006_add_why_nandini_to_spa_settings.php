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
            $table->string('why_nandini_eyebrow')->nullable()->after('wellness_philosophy_image_alt');
            $table->text('why_nandini_heading')->nullable()->after('why_nandini_eyebrow');
            $table->json('why_nandini_items')->nullable()->after('why_nandini_heading');
        });

        DB::table('spa_settings')->where('id', 1)->update([
            'why_nandini_eyebrow' => 'WHY NANDINI',
            'why_nandini_heading' => 'WELLNESS ROOTED IN NATURE',
            'why_nandini_items' => json_encode([
                ['icon' => 'jungle', 'title' => 'JUNGLE SANCTUARY', 'description' => 'Treatments surrounded by tropical nature.'],
                ['icon' => 'ritual', 'title' => 'BALINESE RITUALS', 'description' => 'Wellness inspired by traditional Balinese practices.'],
                ['icon' => 'care', 'title' => 'PERSONALISED CARE', 'description' => 'Experiences tailored to individual wellbeing.'],
                ['icon' => 'river', 'title' => 'RIVER-SIDE SERENITY', 'description' => 'A unique spa environment shaped by the jungle landscape.'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->dropColumn(['why_nandini_eyebrow', 'why_nandini_heading', 'why_nandini_items']);
        });
    }
};
