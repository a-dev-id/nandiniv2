<?php

namespace App\Services\Payments\Flywire;

class FlywireStatusMapper
{
    public function paymentStatus(string $flywireStatus): string
    {
        $normalized = strtolower(trim($flywireStatus));

        if ($this->shouldIssue($flywireStatus)) {
            return 'paid';
        }

        return match ($normalized) {
            'cancelled', 'canceled' => 'cancelled',
            'failed', 'rejected' => 'failed',
            'refunded' => 'refunded',
            default => 'processing',
        };
    }

    public function shouldIssue(string $flywireStatus): bool
    {
        $allowed = collect(explode(',', (string) config('services.flywire.issue_on_statuses')))
            ->map(fn(string $status): string => strtolower(trim($status)))
            ->filter()
            ->all();

        return in_array(strtolower(trim($flywireStatus)), $allowed, true);
    }
}
