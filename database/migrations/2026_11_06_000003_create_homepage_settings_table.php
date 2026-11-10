<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table): void {
            $table->id();

            foreach (['villas', 'experiences', 'offers', 'dining', 'spa', 'adventures'] as $section) {
                $table->string($section.'_heading', 255);
                $table->text($section.'_description');
                $table->string($section.'_action_label', 100);
            }

            foreach (['experiences', 'offers', 'adventures'] as $section) {
                $table->string($section.'_footer_label', 100);
            }

            $table->string('featured_suite_action_label', 100);
            $table->string('guest_reviews_heading', 255);
            $table->string('guest_reviews_action_label', 100);
            $table->timestamps();
        });

        DB::table('homepage_settings')->insert([
            'id' => 1,
            'villas_heading' => 'Private Jungle Villas & Royal Suites',
            'villas_description' => 'Stay in private jungle villas in Ubud and spacious royal suites surrounded by rainforest, tropical greenery, and the calm atmosphere of Bali. Each accommodation at Nandini Jungle by Hanging Gardens is designed for comfort, privacy, and warm Balinese character, creating a refined jungle retreat for couples, families, and guests seeking space to reconnect with nature.',
            'villas_action_label' => 'More Details',
            'experiences_heading' => 'Experiences Beyond the Stay',
            'experiences_description' => "Enhance your stay at Nandini Jungle by Hanging Gardens with curated experiences in the heart of Bali's rainforest. From romantic dining and jungle celebrations to floating breakfast and calming spa rituals, each experience is designed to enrich your Ubud retreat with nature, romance, and authentic Balinese hospitality.",
            'experiences_action_label' => 'Explore More',
            'experiences_footer_label' => 'Explore More',
            'offers_heading' => 'Curated Escapes at Nandini Jungle',
            'offers_description' => 'Discover exclusive offers at Nandini Jungle by Hanging Gardens, created for guests who want more than a short getaway. From extended stays to twin-island escapes, each package combines nature, comfort, and meaningful experiences for a memorable Bali jungle resort stay.',
            'offers_action_label' => 'More Details',
            'offers_footer_label' => 'Explore More',
            'dining_heading' => 'Culinary Journeys',
            'dining_description' => 'Dine surrounded by rainforest views and warm Balinese hospitality at Nandini Jungle by Hanging Gardens. From authentic flavors to afternoon tea and relaxed drinks, every culinary experience is crafted to complement your stay at a peaceful jungle resort in Ubud.',
            'dining_action_label' => 'Explore More',
            'spa_heading' => 'Jungle Spa & Wellness',
            'spa_description' => 'Restore body, mind, and spirit at Essence Spa, where each treatment is inspired by the natural surroundings of Nandini Jungle by Hanging Gardens. Set within a quiet rainforest environment, our spa experiences combine nature, relaxation, and Balinese wellness traditions for a calming wellness retreat in Ubud.',
            'spa_action_label' => 'Explore More',
            'adventures_heading' => 'Bali Adventure Journeys',
            'adventures_description' => "Discover Bali's natural landscapes beyond Nandini Jungle by Hanging Gardens. From Ayung River rafting and ATV adventures to Mount Batur sunrise tours, each journey offers a different way to explore the island, with convenient access from your jungle retreat in Payangan, near Ubud.",
            'adventures_action_label' => 'More Details',
            'adventures_footer_label' => 'Explore More',
            'featured_suite_action_label' => 'More Details',
            'guest_reviews_heading' => 'What Our Guests Say',
            'guest_reviews_action_label' => 'See More',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
