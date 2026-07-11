<?php

namespace App\Services\Voucher;

use App\Contracts\Payments\PaymentGateway;
use App\Models\VoucherOrder;
use App\Services\Voucher\Cart\VoucherCartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class VoucherCheckoutService
{
    public function __construct(
        private readonly VoucherCartService $cart,
        private readonly VoucherOrderService $orders,
        private readonly PaymentGateway $gateway,
    ) {
    }

    public function createOrderAndPayment(array $data, $member = null): VoucherOrder
    {
        $cart = $this->cart->refresh();

        if ($cart['lines']->isEmpty()) {
            throw new RuntimeException('Your voucher cart is empty.');
        }

        $accessToken = Str::random(48);

        $order = DB::transaction(function () use ($data, $member, $cart, $accessToken): VoucherOrder {
            $order = VoucherOrder::query()->create([
                'member_id' => $member?->id,
                'order_number' => $this->orders->makeOrderNumber(),
                'access_token_hash' => hash('sha256', $accessToken),
                'purchaser_first_name' => $data['purchaser_first_name'],
                'purchaser_last_name' => $data['purchaser_last_name'],
                'purchaser_email' => strtolower($data['purchaser_email']),
                'purchaser_phone' => $data['purchaser_phone'] ?? null,
                'billing_country_code' => strtoupper($data['billing_country_code']),
                'currency' => $cart['currency'],
                'subtotal' => $cart['subtotal'],
                'discount_amount' => $cart['discount'],
                'total_amount' => $cart['total'],
                'payment_status' => 'pending',
                'order_status' => 'pending_payment',
                'metadata' => ['guest_access_token_created' => true],
            ]);

            foreach ($cart['lines'] as $line) {
                $voucher = $line['voucher'];
                $order->items()->create([
                    'voucher_id' => $voucher->id,
                    'voucher_title' => $voucher->title,
                    'voucher_sku' => $voucher->sku,
                    'voucher_type' => $voucher->voucher_type,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                    'currency' => $line['currency'],
                    'recipient_name' => $line['recipient_name'],
                    'recipient_email' => $line['recipient_email'],
                    'personal_message' => $line['personal_message'] ?: null,
                    'delivery_method' => $line['delivery_method'],
                    'scheduled_delivery_at' => ! empty($line['delivery_date']) ? $line['delivery_date'] : null,
                    'voucher_snapshot' => [
                        'title' => $voucher->title,
                        'slug' => $voucher->slug,
                        'sku' => $voucher->sku,
                        'voucher_type' => $voucher->voucher_type,
                        'face_value' => $voucher->face_value,
                        'selling_price' => $voucher->selling_price,
                        'discount_percentage' => $voucher->discount_percentage,
                        'discounted_price' => $voucher->discounted_price,
                        'currency' => $voucher->currency,
                        'price_type' => $voucher->price_type,
                        'unit_type' => $voucher->unit_type,
                        'description' => $voucher->description,
                        'inclusions' => $voucher->inclusions,
                        'terms_conditions' => $voucher->terms_conditions,
                        'validity_type' => $voucher->validity_type,
                        'validity_days' => $voucher->validity_days,
                        'fixed_valid_from' => $voucher->fixed_valid_from?->toDateString(),
                        'fixed_valid_until' => $voucher->fixed_valid_until?->toDateString(),
                        'allow_partial_redemption' => $voucher->allow_partial_redemption,
                    ],
                ]);
            }

            return $order;
        });

        $session = $this->gateway->createCheckoutSession($order);

        $order->forceFill([
            'payment_status' => 'payment_session_created',
            'flywire_checkout_session_id' => $session->sessionId,
            'flywire_hosted_form_url' => $session->redirectUrl,
            'metadata' => array_merge($order->metadata ?? [], [
                'flywire_session_response' => $session->raw,
            ]),
        ])->save();

        session()->put('voucher.order_access.' . $order->order_number, $accessToken);
        $this->cart->clear();

        return $order->fresh('items');
    }
}
