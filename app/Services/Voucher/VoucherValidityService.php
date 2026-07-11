<?php

namespace App\Services\Voucher;

use App\Models\VoucherOrderItem;
use Carbon\CarbonImmutable;

class VoucherValidityService
{
    public function datesFor(VoucherOrderItem $item): array
    {
        $snapshot = $item->voucher_snapshot ?? [];
        $issuedAt = CarbonImmutable::now();

        if (($snapshot['validity_type'] ?? null) === 'fixed_date') {
            return [
                'valid_from' => ! empty($snapshot['fixed_valid_from']) ? CarbonImmutable::parse($snapshot['fixed_valid_from'])->toDateString() : null,
                'expires_at' => ! empty($snapshot['fixed_valid_until']) ? CarbonImmutable::parse($snapshot['fixed_valid_until'])->toDateString() : null,
            ];
        }

        if (($snapshot['validity_type'] ?? null) === 'days_after_issue' && ! empty($snapshot['validity_days'])) {
            return [
                'valid_from' => $issuedAt->toDateString(),
                'expires_at' => $issuedAt->addDays((int) $snapshot['validity_days'])->toDateString(),
            ];
        }

        return [
            'valid_from' => $issuedAt->toDateString(),
            'expires_at' => null,
        ];
    }
}
