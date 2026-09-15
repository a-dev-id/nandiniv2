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
            $table->string('wellness_philosophy_eyebrow')->nullable()->after('information_bar_items');
            $table->text('wellness_philosophy_heading')->nullable()->after('wellness_philosophy_eyebrow');
            $table->text('wellness_philosophy_description')->nullable()->after('wellness_philosophy_heading');
            $table->string('wellness_philosophy_image')->nullable()->after('wellness_philosophy_description');
            $table->string('wellness_philosophy_image_alt')->nullable()->after('wellness_philosophy_image');
        });

        DB::table('spa_settings')->where('id', 1)->update([
            'wellness_philosophy_eyebrow' => 'OUR WELLNESS PHILOSOPHY',
            'wellness_philosophy_heading' => "A SACRED PAUSE\nIN THE JUNGLE",
            'wellness_philosophy_description' => 'At Nandini Jungle, wellness is a harmonious journey of body, mind and spirit, inspired by Balinese traditions and the healing power of nature. Our spa experiences invite you to slow down, reconnect and embrace a deeper sense of wellbeing.',
            'wellness_philosophy_image' => 'pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
            'wellness_philosophy_image_alt' => 'A flower-filled spa jacuzzi surrounded by candles and tropical jungle',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'wellness_philosophy_eyebrow',
                'wellness_philosophy_heading',
                'wellness_philosophy_description',
                'wellness_philosophy_image',
                'wellness_philosophy_image_alt',
            ]);
        });
    }
};
