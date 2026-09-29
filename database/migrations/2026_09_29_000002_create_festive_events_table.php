<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festive_events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(1)->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->string('hero_image')->nullable();
            $table->string('hero_image_alt')->nullable();
            $table->string('hero_eyebrow')->nullable();
            $table->text('hero_heading')->nullable();
            $table->string('hero_subheading')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_price')->nullable();
            $table->string('hero_button_label')->nullable();
            $table->text('hero_button_url')->nullable();

            $table->boolean('information_visible')->default(true);
            $table->json('information_items')->nullable();

            $table->boolean('menu_visible')->default(true);
            $table->string('menu_eyebrow')->nullable();
            $table->text('menu_heading')->nullable();
            $table->text('menu_description')->nullable();
            $table->json('menu_items')->nullable();

            $table->boolean('programme_visible')->default(false);
            $table->string('programme_image')->nullable();
            $table->string('programme_image_alt')->nullable();
            $table->string('programme_eyebrow')->nullable();
            $table->text('programme_heading')->nullable();
            $table->json('programme_items')->nullable();

            $table->boolean('reservation_visible')->default(true);
            $table->string('reservation_eyebrow')->nullable();
            $table->text('reservation_heading')->nullable();
            $table->text('reservation_description')->nullable();
            $table->string('reservation_button_label')->nullable();
            $table->text('reservation_button_url')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('festive_events')->insert([
            [
                'title' => 'Christmas Dinner',
                'slug' => 'christmas-dinner',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Christmas Dinner | Nandini Jungle by Hanging Gardens',
                'meta_description' => 'Celebrate Christmas Eve with a festive multi-course dinner, Balinese performances and warm moments at Nandini Jungle.',
                'hero_image' => '/images/festive/2026/christmas-dining.jpg',
                'hero_image_alt' => 'A Christmas in the Jungle',
                'hero_eyebrow' => '24 DECEMBER 2026',
                'hero_heading' => "A Christmas\nin the Jungle",
                'hero_subheading' => 'CHRISTMAS CELEBRATION AT NANDINI JUNGLE',
                'hero_description' => 'Surrounded by the quiet beauty of the jungle, share an evening of beautifully prepared dishes, warm conversations, and festive moments together.',
                'hero_price' => 'IDR 1,800,000++ per person',
                'hero_button_label' => 'RESERVE NOW',
                'hero_button_url' => '#reserve',
                'information_visible' => true,
                'information_items' => json_encode([
                    ['label' => 'DATE', 'value' => '24 December 2026'],
                    ['label' => 'EXPERIENCE', 'value' => 'Christmas Eve Dinner'],
                    ['label' => 'LOCATION', 'value' => 'Wild Ginger Restaurant / Nandini Jungle'],
                    ['label' => 'DINING', 'value' => 'Festive Multi-Course Dinner'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'menu_visible' => true,
                'menu_eyebrow' => 'CHRISTMAS EVE',
                'menu_heading' => 'Modern Surf and Turf',
                'menu_description' => 'A festive multi-course experience combining refined flavours, premium ingredients, and thoughtful presentation.',
                'menu_items' => json_encode([
                    ['type' => 'dish', 'title' => 'Chawanmusi Egg', 'description' => 'Salmon Row – Egg Custard – Spring Onion', 'image' => '/images/festive/2026/Dish/CHAWANMUSI-EGG.jpg', 'image_alt' => 'Chawanmusi Egg'],
                    ['type' => 'dish', 'title' => 'Octopus Dumpling', 'description' => 'Marinated Octopus – Chili Emulsion – Celery Compote', 'image' => '/images/festive/2026/Dish/OCTOPUS%20DUMPLING.jpg', 'image_alt' => 'Octopus Dumpling'],
                    ['type' => 'dish', 'title' => 'Cured Salmon', 'description' => 'Mushroom Jelly – Comfit Tomato Cherry – Onion Puree – Pickle Simeji', 'image' => '/images/festive/2026/Dish/CURED%20SALMON.jpg', 'image_alt' => 'Cured Salmon'],
                    ['type' => 'dish', 'title' => 'Beet Root Tartare', 'description' => 'Miso Tomato Cherry – So Vide Avocado – Pickle Red Onion', 'image' => '/images/festive/2026/Dish/BEET%20ROOT%20TARTARE.jpg', 'image_alt' => 'Beet Root Tartare'],
                    ['type' => 'dish', 'title' => 'Magret de Canard', 'description' => 'Sweet Potato Gnocchi – Duck Sauce – Baby Carrot – Asparagus', 'image' => '/images/festive/2026/Dish/MAGRET%20DE%20CANARD.jpg', 'image_alt' => 'Magret de Canard'],
                    ['type' => 'dish', 'title' => '“48 Hours Wagyu” Short Rib', 'description' => '“Ketan” Risotto – Roasted Bone Marrow – Broccolini – Soy Chili Emulsion – Edible', 'image' => '/images/festive/2026/Dish/%E2%80%9C48%20HOURS%20WAGYU%E2%80%9D%20SHORT%20RIB.jpg', 'image_alt' => '48 Hours Wagyu Short Rib'],
                    ['type' => 'dish', 'title' => 'Dark Chocolate Jelly', 'description' => 'Orange Puree – Chocolate Mousse – Cacao Crumble – Berry Salsa', 'image' => '/images/festive/2026/Dish/DARK%20CHOCOLATE%20JELLY.jpg', 'image_alt' => 'Dark Chocolate Jelly'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'programme_visible' => true,
                'programme_image' => '/images/festive/2026/christmas-dining.jpg',
                'programme_image_alt' => 'Christmas festive programme',
                'programme_eyebrow' => 'CHRISTMAS EVENING',
                'programme_heading' => 'Programme of the Evening',
                'programme_items' => json_encode([
                    ['time' => '06:00 PM – 07:00 PM', 'activity' => 'Cocktail & Canape Soiree at Bar & Lounge'],
                    ['time' => '07:00 PM – 08:00 PM', 'activity' => 'Christmas Dinner & Balinese Dance Performance'],
                    ['time' => '07:45 PM – 08:00 PM', 'activity' => 'GM’s Speech & Tree Lighting Ceremony'],
                    ['time' => '08:00 PM – 08:30 PM', 'activity' => 'Nandini Jungle’s Angels Choir'],
                    ['time' => '08:30 PM – 09:00 PM', 'activity' => 'Balinese Social Dance'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'reservation_visible' => true,
                'reservation_eyebrow' => 'CHRISTMAS AT NANDINI',
                'reservation_heading' => 'Reserve Your Table',
                'reservation_description' => 'Join us for a memorable Christmas evening at Nandini Jungle.',
                'reservation_button_label' => 'RESERVE NOW',
                'reservation_button_url' => 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to reserve the Christmas Dinner at Nandini Jungle.'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'New Year Dinner',
                'slug' => 'new-year-dinner',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'New Year Dinner | Nandini Jungle by Hanging Gardens',
                'meta_description' => 'Welcome the new year with an exquisite multi-course dinner and an intimate evening in the heart of the jungle.',
                'hero_image' => '/images/festive/2026/new-year-dining.jpg',
                'hero_image_alt' => 'A Night to Begin Anew',
                'hero_eyebrow' => '31 DECEMBER 2026',
                'hero_heading' => "A Night to\nBegin Anew",
                'hero_subheading' => 'NEW YEAR’S EVE CELEBRATION',
                'hero_description' => 'As the year comes to a close, slow down and enjoy the evening in the heart of the jungle. Gather around the table, share good food and conversation, and welcome the year ahead.',
                'hero_price' => 'IDR 2,200,000++ per person',
                'hero_button_label' => 'RESERVE NOW',
                'hero_button_url' => '#reserve',
                'information_visible' => true,
                'information_items' => json_encode([
                    ['label' => 'DATE', 'value' => '31 December 2026'],
                    ['label' => 'EXPERIENCE', 'value' => 'New Year Eve'],
                    ['label' => 'LOCATION', 'value' => 'Nandini Jungle'],
                    ['label' => 'DINING', 'value' => 'Festive Multi-Course Dinner'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'menu_visible' => true,
                'menu_eyebrow' => 'NEW YEAR EVE',
                'menu_heading' => 'Dinner Menu',
                'menu_description' => 'An exquisite multi-course dining experience, crafted with premium ingredients and contemporary flavours.',
                'menu_items' => json_encode([
                    ['type' => 'dish', 'title' => 'Blood Orange Fish Carpaccio', 'description' => 'King Fish – Orange Dressing – Calamansi Jelly', 'image' => '/images/festive/2026/Dish/BLOOD%20ORANGE%20FISH%20CARPACCIO.jpg', 'image_alt' => 'Blood Orange Fish Carpaccio'],
                    ['type' => 'dish', 'title' => 'Blue Crab', 'description' => 'Jicama – Granny Smith Apple – Celery Pickle – Tomato Cherry', 'image' => '/images/festive/2026/Dish/BLUE%20CRAB.jpg', 'image_alt' => 'Blue Crab'],
                    ['type' => 'intermezzo', 'label' => 'INTERMEZZO', 'title' => 'Water Melon and Lemon Basil Sorbet', 'description' => null, 'image' => null, 'image_alt' => null],
                    ['type' => 'dish', 'title' => 'Surf and Turf', 'description' => 'Mushroom Puree – Crispy Onion – Asparagus – Chimichurri – Nasturtium', 'image' => '/images/festive/2026/Dish/SURF%20AND%20TURF.jpg', 'image_alt' => 'Surf and Turf'],
                    ['type' => 'dish', 'title' => 'Soy Black Cod', 'description' => 'Chili Pepper Puree – Grilled Asparagus – Broccolini – Salsa Verde – Cress Salad', 'image' => '/images/festive/2026/Dish/SOY%20BLACK%20COD.jpg', 'image_alt' => 'Soy Black Cod'],
                    ['type' => 'dish', 'title' => 'V3+ Wagyu Tenderloin', 'description' => 'Sweet Potato Grattan – Truffle Demi Glass – Chard King Oyster Mushroom', 'image' => '/images/festive/2026/Dish/V3%2B%20WAGYU%20TENDERLOIN.jpg', 'image_alt' => 'V3+ Wagyu Tenderloin'],
                    ['type' => 'dish', 'label' => 'DESSERT', 'title' => 'Tape Ketan Panna Cotta', 'description' => 'Fermented Glutinous Rice – Mango Compote – Yogurt Ice Cream', 'image' => '/images/festive/2026/Dish/Tape%20ketan%20panna%20cotta.jpg', 'image_alt' => 'Tape Ketan Panna Cotta'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'programme_visible' => false,
                'programme_image' => null,
                'programme_image_alt' => null,
                'programme_eyebrow' => null,
                'programme_heading' => null,
                'programme_items' => json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'reservation_visible' => true,
                'reservation_eyebrow' => 'NEW YEAR AT NANDINI',
                'reservation_heading' => 'Reserve Your Table',
                'reservation_description' => 'Celebrate the new year with an exquisite dining experience at Nandini Jungle.',
                'reservation_button_label' => 'RESERVE NOW',
                'reservation_button_url' => 'https://wa.me/6281236871170?text='.rawurlencode("Hello, I would like to reserve the New Year's Eve Dinner at Nandini Jungle."),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('festive_events');
    }
};
