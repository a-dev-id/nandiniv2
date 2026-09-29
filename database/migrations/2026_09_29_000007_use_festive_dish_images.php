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

        $images = [
            'Octopus Dumpling' => '/images/festive/2026/Dish/OCTOPUS%20DUMPLING.jpg',
            'Cured Salmon' => '/images/festive/2026/Dish/CURED%20SALMON.jpg',
            'Beet Root Tartare' => '/images/festive/2026/Dish/BEET%20ROOT%20TARTARE.jpg',
            'Magret de Canard' => '/images/festive/2026/Dish/MAGRET%20DE%20CANARD.jpg',
            '“48 Hours Wagyu” Short Rib' => '/images/festive/2026/Dish/%E2%80%9C48%20HOURS%20WAGYU%E2%80%9D%20SHORT%20RIB.jpg',
            'Dark Chocolate Jelly' => '/images/festive/2026/Dish/DARK%20CHOCOLATE%20JELLY.jpg',
            'Chawanmusi Egg' => '/images/festive/2026/Dish/CHAWANMUSI-EGG.jpg',
            'Blood Orange Fish Carpaccio' => '/images/festive/2026/Dish/BLOOD%20ORANGE%20FISH%20CARPACCIO.jpg',
            'Blue Crab' => '/images/festive/2026/Dish/BLUE%20CRAB.jpg',
            'Surf and Turf' => '/images/festive/2026/Dish/SURF%20AND%20TURF.jpg',
            'Soy Black Cod' => '/images/festive/2026/Dish/SOY%20BLACK%20COD.jpg',
            'V3+ Wagyu Tenderloin' => '/images/festive/2026/Dish/V3%2B%20WAGYU%20TENDERLOIN.jpg',
            'Tape Ketan Panna Cotta' => '/images/festive/2026/Dish/Tape%20ketan%20panna%20cotta.jpg',
        ];

        DB::table('festive_events')->orderBy('id')->get()->each(function (object $event) use ($images): void {
            $items = json_decode((string) $event->menu_items, true) ?: [];

            foreach ($items as &$item) {
                $title = (string) ($item['title'] ?? '');

                if (isset($images[$title])) {
                    $item['image'] = $images[$title];
                }
            }

            DB::table('festive_events')->where('id', $event->id)->update([
                'menu_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('festive_events')) {
            return;
        }

        $fallbacks = [
            'christmas-dinner' => [
                'Octopus Dumpling' => '/images/festive/2026/menu/christmas-01.jpg',
                'Cured Salmon' => '/images/festive/2026/menu/christmas-02.jpg',
                'Beet Root Tartare' => '/images/festive/2026/menu/christmas-03.jpg',
                'Magret de Canard' => '/images/festive/2026/menu/christmas-04.jpg',
                '“48 Hours Wagyu” Short Rib' => '/images/festive/2026/menu/christmas-05.jpg',
                'Dark Chocolate Jelly' => '/images/festive/2026/menu/christmas-06.jpg',
            ],
            'new-year-dinner' => [
                'Chawanmusi Egg' => '/images/festive/2026/menu/new-year-01.jpg',
                'Blood Orange Fish Carpaccio' => '/images/festive/2026/menu/new-year-02.jpg',
                'Blue Crab' => '/images/festive/2026/menu/new-year-03.jpg',
                'Surf and Turf' => '/images/festive/2026/menu/new-year-04.jpg',
                'Soy Black Cod' => '/images/festive/2026/menu/new-year-05.jpg',
                'V3+ Wagyu Tenderloin' => '/images/festive/2026/menu/new-year-06.jpg',
                'Tape Ketan Panna Cotta' => '/images/festive/2026/menu/new-year-07.jpg',
            ],
        ];

        DB::table('festive_events')->orderBy('id')->get()->each(function (object $event) use ($fallbacks): void {
            $images = $fallbacks[$event->slug] ?? [];
            $items = json_decode((string) $event->menu_items, true) ?: [];

            foreach ($items as &$item) {
                $title = (string) ($item['title'] ?? '');

                if (isset($images[$title])) {
                    $item['image'] = $images[$title];
                }
            }

            DB::table('festive_events')->where('id', $event->id)->update([
                'menu_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        });
    }
};
