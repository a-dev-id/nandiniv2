<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spa_settings')) {
            return;
        }

        $settings = DB::table('spa_settings')->where('id', 1)->first();

        if (! $settings) {
            return;
        }

        $mediaBase = 'https://nandinibali.com/storage/';
        $updates = [];

        foreach ([
            'hero_image',
            'hero_mobile_image',
            'wellness_philosophy_image',
            'signature_image',
            'guest_review_image',
            'booking_cta_image',
        ] as $field) {
            $value = $settings->{$field} ?? null;

            if (is_string($value) && $value !== '' && ! Str::startsWith($value, ['http://', 'https://', '/'])) {
                $updates[$field] = $mediaBase.ltrim($value, '/');
            }
        }

        $journeys = json_decode($settings->wellness_journeys_items ?? '[]', true);

        if (is_array($journeys)) {
            $journeysChanged = false;

            foreach ($journeys as &$journey) {
                $image = $journey['image'] ?? null;

                if (is_string($image) && $image !== '' && ! Str::startsWith($image, ['http://', 'https://', '/'])) {
                    $journey['image'] = $mediaBase.ltrim($image, '/');
                    $journeysChanged = true;
                }
            }
            unset($journey);

            if ($journeysChanged) {
                $updates['wellness_journeys_items'] = json_encode($journeys, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('spa_settings')->where('id', 1)->update($updates);
        }
    }

    public function down(): void
    {
        // Absolute source URLs remain valid and must not be made relative again.
    }
};
