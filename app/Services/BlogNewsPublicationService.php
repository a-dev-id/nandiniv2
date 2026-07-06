<?php

namespace App\Services;

use App\Models\BlogNews;
use Carbon\CarbonInterface;

class BlogNewsPublicationService
{
    public function sync(?CarbonInterface $date = null): array
    {
        $date ??= today();

        $activated = BlogNews::query()
            ->where('is_active', false)
            ->whereNotNull('published_at')
            ->whereDate('published_at', '<=', $date)
            ->update(['is_active' => true]);

        $deactivatedScheduled = BlogNews::query()
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->whereDate('published_at', '>', $date)
            ->update(['is_active' => false]);

        return [
            'activated' => $activated,
            'deactivated_scheduled' => $deactivatedScheduled,
        ];
    }
}
