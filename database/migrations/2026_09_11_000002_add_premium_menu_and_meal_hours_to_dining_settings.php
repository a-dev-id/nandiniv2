<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->string('visit_premium_menu_label')->nullable()->after('visit_food_menu_url');
            $table->text('visit_premium_menu_url')->nullable()->after('visit_premium_menu_label');
        });

        $settings = DB::table('dining_settings')->where('id', 1)->first();

        if (! $settings) {
            return;
        }

        $items = json_decode($settings->visit_information_items ?: '[]', true) ?: [];

        foreach ($items as &$item) {
            if (
                strcasecmp(trim((string) ($item['label'] ?? '')), 'Opening Hours') === 0
                && in_array(trim((string) ($item['value'] ?? '')), ['', '7.00 AM – 10.00 PM', '7.00 AM - 10.00 PM'], true)
            ) {
                $item['value'] = "Breakfast: 07:00 AM – 10:30 AM\nLunch: 12:00 PM – 03:00 PM\nDinner: 06:30 PM – 10:30 PM";
            }
        }
        unset($item);

        DB::table('dining_settings')->where('id', 1)->update([
            'visit_information_items' => json_encode($items, JSON_UNESCAPED_UNICODE),
            'visit_food_menu_label' => 'View Menu',
            'visit_food_menu_url' => 'https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing',
            'visit_premium_menu_label' => 'Premium Menu',
            'visit_premium_menu_url' => 'https://drive.google.com/file/d/16XoyEOdlRzFfN2Ca30THHhNfw8OCxWDN/view?usp=sharing',
            'visit_beverage_menu_label' => 'Beverage List',
            'visit_beverage_menu_url' => 'https://drive.google.com/file/d/1Xm5YhSbX18muQQTdrLvNLYd7cgFw5EaS/view?usp=sharing',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->dropColumn(['visit_premium_menu_label', 'visit_premium_menu_url']);
        });
    }
};
