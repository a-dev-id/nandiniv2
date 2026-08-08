<?php

namespace App\Services\Affiliate\Click;

use App\Models\AffiliateClickEvent;
use App\Models\AffiliateUniqueClick;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CleanupAffiliateClickEventsService
{
    /** @return array{events: int, unique_markers: int, retention_days: int} */
    public function cleanup(int $retentionDays): array
    {
        if ($retentionDays <= 0) {
            throw new InvalidArgumentException('Affiliate click retention must be a positive number of days.');
        }

        $cutoff = now()->subDays($retentionDays)->startOfDay();

        return DB::transaction(fn (): array => [
            'events' => AffiliateClickEvent::query()->where('clicked_at', '<', $cutoff)->delete(),
            'unique_markers' => AffiliateUniqueClick::query()->where('click_date', '<', $cutoff->toDateString())->delete(),
            'retention_days' => $retentionDays,
        ]);
    }
}
