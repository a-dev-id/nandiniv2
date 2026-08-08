<?php

namespace App\Services\Affiliate\Operations;

use App\Models\AffiliateOperationalState;

class AffiliateOperationalStateService
{
    public function attempted(string $key, string $summary = 'Started'): AffiliateOperationalState
    {
        return AffiliateOperationalState::query()->updateOrCreate(['key' => $key], [
            'status' => 'running', 'summary' => $summary, 'last_attempted_at' => now(),
        ]);
    }

    public function succeeded(string $key, string $summary, array $metadata = []): AffiliateOperationalState
    {
        return AffiliateOperationalState::query()->updateOrCreate(['key' => $key], [
            'status' => 'success', 'summary' => $summary, 'last_attempted_at' => now(), 'last_successful_at' => now(), 'metadata' => $metadata ?: null,
        ]);
    }

    public function failed(string $key, string $summary, array $metadata = []): AffiliateOperationalState
    {
        return AffiliateOperationalState::query()->updateOrCreate(['key' => $key], [
            'status' => 'failed', 'summary' => $summary, 'last_attempted_at' => now(), 'metadata' => $metadata ?: null,
        ]);
    }
}
