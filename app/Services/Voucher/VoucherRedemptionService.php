<?php

namespace App\Services\Voucher;

use App\Models\IssuedVoucher;
use App\Models\VoucherRedemption;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VoucherRedemptionService
{
    public function redeem(IssuedVoucher $issuedVoucher, array $data, $user = null): VoucherRedemption
    {
        return DB::transaction(function () use ($issuedVoucher, $data, $user): VoucherRedemption {
            $voucher = IssuedVoucher::query()->lockForUpdate()->findOrFail($issuedVoucher->id);

            if (! in_array($voucher->status, ['active', 'partially_redeemed'], true)) {
                throw new InvalidArgumentException('Voucher is not redeemable.');
            }

            if ($voucher->expires_at && $voucher->expires_at->isPast()) {
                throw new InvalidArgumentException('Voucher has expired.');
            }

            $allowPartial = (bool) data_get($voucher->orderItem?->voucher_snapshot, 'allow_partial_redemption', false);
            $balanceBefore = (int) ($voucher->remaining_value ?? 0);
            $amount = $allowPartial ? (int) ($data['amount'] ?? $balanceBefore) : $balanceBefore;
            $balanceAfter = max(0, $balanceBefore - $amount);

            if ($amount <= 0 || $amount > $balanceBefore) {
                throw new InvalidArgumentException('Invalid redemption amount.');
            }

            $redemption = $voucher->redemptions()->create([
                'redeemed_by_user_id' => $user?->id,
                'redemption_location' => $data['redemption_location'] ?? null,
                'department' => $data['department'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'notes' => $data['notes'] ?? null,
                'redeemed_at' => now(),
            ]);

            $voucher->forceFill([
                'remaining_value' => $balanceAfter,
                'status' => $balanceAfter > 0 ? 'partially_redeemed' : 'redeemed',
                'redeemed_at' => $balanceAfter > 0 ? null : now(),
            ])->save();

            return $redemption;
        });
    }
}
