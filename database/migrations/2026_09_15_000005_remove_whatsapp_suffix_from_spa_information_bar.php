<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->updateReservationValues(removeSuffix: true);
    }

    public function down(): void
    {
        $this->updateReservationValues(removeSuffix: false);
    }

    private function updateReservationValues(bool $removeSuffix): void
    {
        DB::table('spa_settings')
            ->select(['id', 'information_bar_items'])
            ->orderBy('id')
            ->each(function (object $settings) use ($removeSuffix): void {
                $items = json_decode((string) $settings->information_bar_items, true);

                if (! is_array($items)) {
                    return;
                }

                foreach ($items as &$item) {
                    if (! is_array($item) || ! is_string($item['value'] ?? null)) {
                        continue;
                    }

                    if ($removeSuffix) {
                        $item['value'] = preg_replace('/\s*\(whatsapp\)\s*$/iu', '', $item['value']) ?? $item['value'];

                        continue;
                    }

                    if (strcasecmp((string) ($item['label'] ?? ''), 'Reservations') === 0
                        && ! str_contains(strtolower($item['value']), 'whatsapp')) {
                        $item['value'] .= "\n(WhatsApp)";
                    }
                }
                unset($item);

                DB::table('spa_settings')->where('id', $settings->id)->update([
                    'information_bar_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            });
    }
};
