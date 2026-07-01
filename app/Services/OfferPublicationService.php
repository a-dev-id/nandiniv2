<?php

namespace App\Services;

use App\Models\Offer;
use Carbon\CarbonInterface;

class OfferPublicationService
{
    public function sync(?CarbonInterface $date = null): array
    {
        $date ??= today();

        $activated = Offer::query()
            ->where('is_active', false)
            ->whereNotNull('valid_start_date')
            ->whereDate('valid_start_date', '<=', $date)
            ->where(function ($query) use ($date): void {
                $query
                    ->whereNull('valid_end_date')
                    ->orWhereDate('valid_end_date', '>=', $date);
            })
            ->update(['is_active' => true]);

        $deactivatedScheduled = Offer::query()
            ->where('is_active', true)
            ->whereNotNull('valid_start_date')
            ->whereDate('valid_start_date', '>', $date)
            ->update(['is_active' => false]);

        $deactivatedExpired = Offer::query()
            ->where('is_active', true)
            ->whereNotNull('valid_end_date')
            ->whereDate('valid_end_date', '<', $date)
            ->update(['is_active' => false]);

        return [
            'activated' => $activated,
            'deactivated_scheduled' => $deactivatedScheduled,
            'deactivated_expired' => $deactivatedExpired,
        ];
    }
}
