<?php

namespace App\Services\Voucher;

use App\Models\IssuedVoucher;
use App\Models\VoucherOrder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class VoucherIssuer
{
    public function __construct(
        private readonly VoucherCodeGenerator $codes,
        private readonly VoucherValidityService $validity,
        private readonly VoucherEmailService $emails,
    ) {}

    public function issueForOrder(VoucherOrder $order): void
    {
        DB::transaction(function () use ($order): void {
            $order = VoucherOrder::query()->with('items.issuedVouchers')->lockForUpdate()->findOrFail($order->id);

            foreach ($order->items as $item) {
                $existing = $item->issuedVouchers()->count();
                $needed = max(0, (int) $item->quantity - $existing);

                for ($i = 0; $i < $needed; $i++) {
                    $this->createIssuedVoucher($order, $item);
                }
            }

            $order->forceFill([
                'payment_status' => 'paid',
                'order_status' => 'completed',
                'paid_at' => $order->paid_at ?: now(),
                'completed_at' => $order->completed_at ?: now(),
            ])->save();
        });

        DB::afterCommit(function () use ($order): void {
            $this->deliverUndeliveredForOrder($order->id);
        });
    }

    public function deliverUndeliveredForOrder(int $orderId): void
    {
        VoucherOrder::query()
            ->find($orderId)?->issuedVouchers()
            ->whereNull('delivered_at')
            ->with('orderItem.order')
            ->each(fn (IssuedVoucher $voucher) => $this->emails->sendIssued($voucher));
    }

    private function createIssuedVoucher(VoucherOrder $order, $item): IssuedVoucher
    {
        $snapshot = $item->voucher_snapshot ?? [];
        $dates = $this->validity->datesFor($item);

        for ($attempt = 0; $attempt < 8; $attempt++) {
            $token = $this->codes->verificationToken();

            try {
                return IssuedVoucher::query()->create([
                    'voucher_order_item_id' => $item->id,
                    'voucher_id' => $item->voucher_id,
                    'member_id' => $order->member_id,
                    'voucher_code' => $this->codes->code((string) $item->voucher_type),
                    'verification_token_hash' => hash('sha256', $token),
                    'recipient_name' => $item->recipient_name,
                    'recipient_email' => $item->recipient_email,
                    'title' => $item->voucher_title,
                    'description_snapshot' => $snapshot['description'] ?? null,
                    'terms_snapshot' => $snapshot['terms_conditions'] ?? null,
                    'original_value' => $snapshot['face_value'] ?? $item->unit_price,
                    'remaining_value' => $snapshot['face_value'] ?? $item->unit_price,
                    'currency' => $item->currency,
                    'issued_at' => now(),
                    'valid_from' => $dates['valid_from'],
                    'expires_at' => $dates['expires_at'],
                    'status' => 'active',
                    'metadata' => [
                        'verification_url' => route('voucher.verify', ['token' => $token]),
                        'gift_from' => $snapshot['gift_from'] ?? null,
                        'purchase_for' => $snapshot['purchase_for'] ?? (
                            ($snapshot['delivery_method'] ?? $item->delivery_method) === 'print_at_resort'
                            || filled($snapshot['gift_from'] ?? null)
                            || strcasecmp((string) $item->recipient_email, (string) $order->purchaser_email) !== 0
                                ? 'gift'
                                : 'self'
                        ),
                        'personal_message' => $item->personal_message,
                        'delivery_method' => $snapshot['delivery_method'] ?? $item->delivery_method ?? 'email',
                        'delivery_fee' => $snapshot['delivery_fee'] ?? 0,
                        'hotel_note' => $snapshot['hotel_note'] ?? null,
                        'price_option' => $snapshot['price_option'] ?? null,
                    ],
                ]);
            } catch (QueryException $e) {
                if ($attempt === 7) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Unable to create a unique voucher code.');
    }
}
