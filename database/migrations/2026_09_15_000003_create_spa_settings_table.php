<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spa_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('reservation_whatsapp')->nullable();
            $table->text('reservation_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_author')->nullable();
            $table->string('meta_site_name')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_mobile_image')->nullable();
            $table->string('hero_image_alt')->nullable();
            $table->string('hero_mobile_image_alt')->nullable();
            $table->string('hero_eyebrow')->nullable();
            $table->text('hero_heading')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_primary_cta_label')->nullable();
            $table->text('hero_primary_cta_url')->nullable();
            $table->string('hero_secondary_cta_label')->nullable();
            $table->text('hero_secondary_cta_url')->nullable();
            $table->json('information_bar_items')->nullable();
            $table->timestamps();
        });

        $spaHome = Schema::hasTable('pages')
            ? DB::table('pages')->where('site', 'spa')->where('slug', 'home')->first()
            : null;

        DB::table('spa_settings')->insert([
            'id' => 1,
            'reservation_whatsapp' => '+62 812 3687 1170',
            'reservation_url' => 'https://wa.me/6281236871170',
            'meta_title' => $spaHome?->meta_title ?: 'Spa & Wellness | Nandini Jungle by Hanging Gardens',
            'meta_description' => $spaHome?->meta_description ?: 'Restore body, mind and soul with deeply restorative spa rituals in the heart of the Ubud jungle.',
            'meta_author' => 'Nandini Jungle by Hanging Gardens',
            'meta_site_name' => 'Nandini Jungle by Hanging Gardens',
            'hero_image' => $spaHome?->hero_image,
            'hero_mobile_image' => $spaHome?->hero_mobile_image,
            'hero_image_alt' => $spaHome?->hero_image_alt,
            'hero_mobile_image_alt' => $spaHome?->hero_mobile_image_alt,
            'hero_eyebrow' => 'WELLNESS AT NANDINI JUNGLE',
            'hero_heading' => "RESTORE IN THE HEART\nOF THE JUNGLE",
            'hero_description' => 'Experience deeply restorative spa rituals inspired by Bali, nature and the surrounding jungle. A serene sanctuary to rebalance your body, mind and soul.',
            'hero_primary_cta_label' => 'BOOK A SPA EXPERIENCE',
            'hero_primary_cta_url' => 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.'),
            'hero_secondary_cta_label' => 'EXPLORE TREATMENTS',
            'hero_secondary_cta_url' => null,
            'information_bar_items' => json_encode([
                ['icon' => 'clock', 'label' => 'Opening Hours', 'value' => '08:00 AM – 10:00 PM', 'link' => null],
                ['icon' => 'calendar', 'label' => 'Booking', 'value' => 'Advance booking recommended', 'link' => null],
                ['icon' => 'location', 'label' => 'Location', 'value' => 'Nandini Jungle, Ubud, Bali', 'link' => null],
                ['icon' => 'phone', 'label' => 'Reservations', 'value' => "+62 812 3687 1170\n(WhatsApp)", 'link' => 'https://wa.me/6281236871170'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('spa_settings');
    }
};
