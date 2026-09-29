<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('festive_events')) {
            return;
        }

        $menus = [
            'christmas-dinner' => [
                ['type' => 'dish', 'title' => 'Octopus Dumpling', 'description' => 'Marinated Octopus – Chili Emulsion – Celery Compote', 'image' => '/images/festive/2026/Dish/OCTOPUS%20DUMPLING.jpg', 'image_alt' => 'Octopus Dumpling'],
                ['type' => 'dish', 'title' => 'Cured Salmon', 'description' => 'Mushroom Jelly – Comfit Tomato Cherry – Onion Puree – Pickle Simeji', 'image' => '/images/festive/2026/Dish/CURED%20SALMON.jpg', 'image_alt' => 'Cured Salmon'],
                ['type' => 'dish', 'title' => 'Beet Root Tartare', 'description' => 'Miso Tomato Cherry – So Vide Avocado – Pickle Red Onion', 'image' => '/images/festive/2026/Dish/BEET%20ROOT%20TARTARE.jpg', 'image_alt' => 'Beet Root Tartare'],
                ['type' => 'dish', 'title' => 'Magret de Canard', 'description' => 'Sweet Potato Gnocchi – Duck Sauce – Baby Carrot – Asparagus', 'image' => '/images/festive/2026/Dish/MAGRET%20DE%20CANARD.jpg', 'image_alt' => 'Magret de Canard'],
                ['type' => 'dish', 'title' => '“48 Hours Wagyu” Short Rib', 'description' => '“Ketan” Risotto – Roasted Bone Marrow – Broccolini – Soy Chili Emulsion – Edible', 'image' => '/images/festive/2026/Dish/%E2%80%9C48%20HOURS%20WAGYU%E2%80%9D%20SHORT%20RIB.jpg', 'image_alt' => '48 Hours Wagyu Short Rib'],
                ['type' => 'dish', 'title' => 'Dark Chocolate Jelly', 'description' => 'Orange Puree – Chocolate Mousse – Cacao Crumble – Berry Salsa', 'image' => '/images/festive/2026/Dish/DARK%20CHOCOLATE%20JELLY.jpg', 'image_alt' => 'Dark Chocolate Jelly'],
            ],
            'new-year-dinner' => [
                ['type' => 'dish', 'title' => 'Chawanmusi Egg', 'description' => 'Salmon Row – Egg Custard – Spring Onion', 'image' => '/images/festive/2026/Dish/CHAWANMUSI-EGG.jpg', 'image_alt' => 'Chawanmusi Egg'],
                ['type' => 'dish', 'title' => 'Blood Orange Fish Carpaccio', 'description' => 'King Fish – Orange Dressing – Calamansi Jelly', 'image' => '/images/festive/2026/Dish/BLOOD%20ORANGE%20FISH%20CARPACCIO.jpg', 'image_alt' => 'Blood Orange Fish Carpaccio'],
                ['type' => 'dish', 'title' => 'Blue Crab', 'description' => 'Jicama – Granny Smith Apple – Celery Pickle – Tomato Cherry', 'image' => '/images/festive/2026/Dish/BLUE%20CRAB.jpg', 'image_alt' => 'Blue Crab'],
                ['type' => 'intermezzo', 'label' => 'INTERMEZZO', 'title' => 'Water Melon and Lemon Basil Sorbet', 'description' => null, 'image' => null, 'image_alt' => null],
                ['type' => 'dish', 'title' => 'Surf and Turf', 'description' => 'Mushroom Puree – Crispy Onion – Asparagus – Chimichurri – Nasturtium', 'image' => '/images/festive/2026/Dish/SURF%20AND%20TURF.jpg', 'image_alt' => 'Surf and Turf'],
                ['type' => 'dish', 'title' => 'Soy Black Cod', 'description' => 'Chili Pepper Puree – Grilled Asparagus – Broccolini – Salsa Verde – Cress Salad', 'image' => '/images/festive/2026/Dish/SOY%20BLACK%20COD.jpg', 'image_alt' => 'Soy Black Cod'],
                ['type' => 'dish', 'title' => 'V3+ Wagyu Tenderloin', 'description' => 'Sweet Potato Grattan – Truffle Demi Glass – Chard King Oyster Mushroom', 'image' => '/images/festive/2026/Dish/V3%2B%20WAGYU%20TENDERLOIN.jpg', 'image_alt' => 'V3+ Wagyu Tenderloin'],
                ['type' => 'dish', 'label' => 'DESSERT', 'title' => 'Tape Ketan Panna Cotta', 'description' => 'Fermented Glutinous Rice – Mango Compote – Yogurt Ice Cream', 'image' => '/images/festive/2026/Dish/Tape%20ketan%20panna%20cotta.jpg', 'image_alt' => 'Tape Ketan Panna Cotta'],
            ],
        ];

        foreach ($menus as $slug => $items) {
            DB::table('festive_events')->where('slug', $slug)->update([
                'menu_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('festive_events')) {
            return;
        }

        $event = DB::table('festive_events')->where('slug', 'new-year-dinner')->first();

        if (! $event) {
            return;
        }

        $items = json_decode((string) $event->menu_items, true) ?: [];

        foreach ($items as &$item) {
            if (($item['title'] ?? null) === 'Chawanmusi Egg') {
                $item['image'] = '/images/festive/2026/Dish/CHAWANMUSI%20EGG%20.jpg';
            }
        }

        DB::table('festive_events')->where('slug', 'new-year-dinner')->update([
            'menu_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }
};
