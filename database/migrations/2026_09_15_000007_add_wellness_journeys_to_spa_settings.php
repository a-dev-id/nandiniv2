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
            $table->string('wellness_journeys_eyebrow')->nullable()->after('why_nandini_items');
            $table->text('wellness_journeys_heading')->nullable()->after('wellness_journeys_eyebrow');
            $table->text('wellness_journeys_description')->nullable()->after('wellness_journeys_heading');
            $table->json('wellness_journeys_items')->nullable()->after('wellness_journeys_description');
        });

        DB::table('spa_settings')->where('id', 1)->update([
            'wellness_journeys_eyebrow' => 'WELLNESS JOURNEYS',
            'wellness_journeys_heading' => 'SACRED JUNGLE WELLNESS JOURNEYS',
            'wellness_journeys_description' => 'Reconnect with your inner self through immersive multi-day experiences, combining traditional Balinese therapy, natural healing and the serene beauty of Nandini Jungle.',
            'wellness_journeys_items' => json_encode([
                [
                    'title' => '2-DAY BALINESE WELLNESS ESCAPE',
                    'description' => 'A two-day journey to revive your energy through a curated blend of Balinese massage, herbal rituals and time in nature.',
                    'image' => 'spas/hero/68d37345-f6e6-4f1d-a962-725cf049fe62.webp',
                    'image_alt' => 'Balinese massage treatment surrounded by the Nandini jungle',
                    'details_label' => 'MORE DETAILS',
                    'details_url' => '/spa-wellness/2-day-balinese-wellness-escape',
                    'book_label' => 'BOOK NOW',
                    'book_url' => 'https://wa.me/6281236871170',
                ],
                [
                    'title' => '3-DAY INNER HARMONY RETREAT',
                    'description' => 'A three-day retreat to restore balance and reconnect with yourself through signature treatments, holistic therapies and mindful rituals.',
                    'image' => 'spas/hero/b5490cd9-d622-4ce2-b483-992ef4ea0c3c.webp',
                    'image_alt' => 'Jungle spa treatment beds prepared for an inner harmony retreat',
                    'details_label' => 'MORE DETAILS',
                    'details_url' => '/spa-wellness/3-day-inner-harmony-retreat',
                    'book_label' => 'BOOK NOW',
                    'book_url' => 'https://wa.me/6281236871170',
                ],
                [
                    'title' => '4-DAY DEEP BALINESE WELLNESS IMMERSION',
                    'description' => 'A four-day immersive experience designed for deep relaxation and renewal, with a combination of traditional therapies, wellness rituals and personalised care.',
                    'image' => 'spas/hero/19d9f7d8-6a93-422d-8a13-b333a2384ff8.webp',
                    'image_alt' => 'Flower bath ritual for a deep Balinese wellness immersion',
                    'details_label' => 'MORE DETAILS',
                    'details_url' => '/spa-wellness/4-day-deep-balinese-wellness-immersion',
                    'book_label' => 'BOOK NOW',
                    'book_url' => 'https://wa.me/6281236871170',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'wellness_journeys_eyebrow',
                'wellness_journeys_heading',
                'wellness_journeys_description',
                'wellness_journeys_items',
            ]);
        });
    }
};
