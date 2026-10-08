<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OFFICIAL_HOURS = '09:00 AM – 10:00 PM';

    public function up(): void
    {
        if (! Schema::hasTable('spa_settings')) {
            return;
        }

        $settings = DB::table('spa_settings')->where('id', 1)->first();

        if (! $settings) {
            return;
        }

        $items = json_decode($settings->information_bar_items ?? '[]', true);

        if (! is_array($items)) {
            $items = [];
        }

        $openingHoursUpdated = false;

        foreach ($items as &$item) {
            $icon = strtolower((string) ($item['icon'] ?? ''));
            $label = strtolower((string) ($item['label'] ?? ''));

            if ($icon === 'clock' || str_contains($label, 'opening') || str_contains($label, 'hours')) {
                $item['value'] = self::OFFICIAL_HOURS;
                $openingHoursUpdated = true;
                break;
            }
        }
        unset($item);

        if (! $openingHoursUpdated) {
            array_unshift($items, [
                'icon' => 'clock',
                'label' => 'Opening Hours',
                'value' => self::OFFICIAL_HOURS,
                'link' => null,
            ]);
        }

        DB::table('spa_settings')->where('id', 1)->update([
            'information_bar_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // The confirmed operating hours must not be reverted to an obsolete value.
    }
};
