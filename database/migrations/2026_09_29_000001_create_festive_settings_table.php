<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festive_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_author')->nullable();
            $table->string('meta_site_name')->nullable();

            $table->boolean('hero_visible')->default(true);
            $table->string('hero_image')->nullable();
            $table->string('hero_image_alt')->nullable();
            $table->string('hero_eyebrow')->nullable();
            $table->text('hero_heading')->nullable();
            $table->string('hero_subheading')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_primary_cta_label')->nullable();
            $table->text('hero_primary_cta_url')->nullable();
            $table->string('hero_secondary_cta_label')->nullable();
            $table->text('hero_secondary_cta_url')->nullable();

            $table->boolean('introduction_visible')->default(true);
            $table->string('introduction_eyebrow')->nullable();
            $table->text('introduction_heading')->nullable();
            $table->text('introduction_description')->nullable();

            $table->boolean('celebrations_visible')->default(true);
            $table->json('celebrations')->nullable();

            $table->boolean('programme_visible')->default(true);
            $table->string('programme_eyebrow')->nullable();
            $table->text('programme_heading')->nullable();
            $table->json('programme_days')->nullable();

            $table->boolean('booking_cta_visible')->default(true);
            $table->string('booking_cta_image')->nullable();
            $table->string('booking_cta_image_alt')->nullable();
            $table->string('booking_cta_eyebrow')->nullable();
            $table->text('booking_cta_heading')->nullable();
            $table->text('booking_cta_description')->nullable();
            $table->string('booking_cta_button_label')->nullable();
            $table->text('booking_cta_button_url')->nullable();
            $table->timestamps();
        });

        DB::table('festive_settings')->insert([
            'id' => 1,
            'meta_title' => 'Festive Season | Nandini Jungle by Hanging Gardens',
            'meta_description' => 'Celebrate Christmas and New Year in the heart of Bali with festive dining and meaningful moments at Nandini Jungle.',
            'meta_author' => 'Nandini Jungle by Hanging Gardens',
            'meta_site_name' => 'Nandini Jungle by Hanging Gardens',
            'hero_visible' => true,
            'hero_image' => '/images/festive/2026/header-landing.jpg',
            'hero_image_alt' => 'Guest overlooking the tropical jungle from a private pool at Nandini Jungle',
            'hero_eyebrow' => 'FESTIVE SEASON',
            'hero_heading' => "A Festive Season\nat Nandini Jungle",
            'hero_subheading' => 'CHRISTMAS & NEW YEAR CELEBRATION',
            'hero_description' => 'Celebrate the season in the heart of the jungle with thoughtfully prepared dining experiences, warm moments, and meaningful celebrations at Nandini Jungle.',
            'hero_primary_cta_label' => 'CHRISTMAS DINNER',
            'hero_primary_cta_url' => '#christmas',
            'hero_secondary_cta_label' => 'NEW YEAR DINNER',
            'hero_secondary_cta_url' => '#new-year',
            'introduction_visible' => true,
            'introduction_eyebrow' => 'A SEASON TO REMEMBER',
            'introduction_heading' => 'Festive Celebrations in the Heart of Bali',
            'introduction_description' => 'This festive season, slow down and celebrate with the people who matter most. Discover specially curated Christmas and New Year dining experiences surrounded by the quiet beauty of Nandini Jungle.',
            'celebrations_visible' => true,
            'celebrations' => json_encode([
                [
                    'anchor' => 'christmas',
                    'image' => '/images/festive/2026/christmas-dining.jpg',
                    'image_alt' => 'Christmas dinner at Nandini Jungle',
                    'date' => '24 DECEMBER 2026',
                    'heading' => 'Christmas at Nandini Jungle',
                    'description' => 'A warm Christmas evening of refined dining, Balinese performances, festive traditions, and meaningful moments together.',
                    'price' => 'IDR 1,800,000++ per person',
                    'button_label' => 'RESERVE CHRISTMAS DINNER',
                    'button_url' => 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to reserve the Christmas Dinner at Nandini Jungle.'),
                ],
                [
                    'anchor' => 'new-year',
                    'image' => '/images/festive/2026/new-year-dining.jpg',
                    'image_alt' => 'New Year dinner at Nandini Jungle',
                    'date' => '31 DECEMBER 2026',
                    'heading' => 'A Night to Begin Anew',
                    'description' => 'Welcome the year ahead with an elegant dining experience, thoughtful flavours, and an intimate evening in the heart of the jungle.',
                    'price' => 'IDR 2,200,000++ per person',
                    'button_label' => 'RESERVE NEW YEAR DINNER',
                    'button_url' => 'https://wa.me/6281236871170?text='.rawurlencode("Hello, I would like to reserve the New Year's Eve Dinner at Nandini Jungle."),
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'programme_visible' => true,
            'programme_eyebrow' => 'FESTIVE PROGRAMME',
            'programme_heading' => 'Christmas Experience at Nandini Jungle',
            'programme_days' => json_encode([
                [
                    'date' => '24 December 2026',
                    'items' => [
                        ['time' => '07:00 AM – 08:00 AM', 'activity' => 'Village Morning Walk'],
                        ['time' => '08:00 AM – 09:10 AM', 'activity' => 'Making Canangsari / Balinese Offering'],
                        ['time' => '06:00 PM – 07:00 PM', 'activity' => 'Cocktail & Canape Soiree at Bar & Lounge'],
                        ['time' => '07:00 PM – 08:00 PM', 'activity' => 'Christmas Dinner & Balinese Dance Performance'],
                        ['time' => '07:45 PM – 08:00 PM', 'activity' => 'GM’s Speech & Tree Lighting Ceremony'],
                        ['time' => '08:00 PM – 08:30 PM', 'activity' => 'Nandini Jungle’s Angels Choir'],
                        ['time' => '08:30 PM – 09:00 PM', 'activity' => 'Balinese Social Dance'],
                    ],
                ],
                [
                    'date' => '25 December 2026',
                    'items' => [
                        ['time' => '08:00 AM – 09:00 AM', 'activity' => 'Meet Santa at Wild Ginger Restaurant'],
                        ['time' => '02:00 PM – 04:00 PM', 'activity' => '“JINGLE & JIGGER” Christmas Private Mixology Class at Mystical Jungle Pool Bar'],
                        ['time' => '03:00 PM – 05:00 PM', 'activity' => 'Exclusive Balinese Afternoon Tea at Bar & Lounge'],
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'booking_cta_visible' => true,
            'booking_cta_image' => '/images/festive/2026/christmas-dining.jpg',
            'booking_cta_image_alt' => 'Festive table at Nandini Jungle',
            'booking_cta_eyebrow' => 'CELEBRATE TOGETHER',
            'booking_cta_heading' => "Celebrate the Festive Season\nin the Heart of Bali",
            'booking_cta_description' => 'Create meaningful moments with festive dining, warm hospitality, and the natural beauty of Nandini Jungle.',
            'booking_cta_button_label' => 'RESERVE A TABLE',
            'booking_cta_button_url' => 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to reserve a table for the festive season at Nandini Jungle.'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('festive_settings');
    }
};
