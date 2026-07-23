<?php

namespace Tests\Feature;

use App\Models\VoucherOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherOrderStatusPollingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_order_page_polls_for_payment_confirmation(): void
    {
        [$order, $token] = $this->orderWithAccess('processing', 'pending_payment');

        $this->get(route('voucher.order.thank-you', [
            'orderNumber' => $order->order_number,
            'token' => $token,
        ]))
            ->assertOk()
            ->assertSee('data-payment-status-poll', false)
            ->assertSee('Checking again in');
    }

    public function test_paid_order_page_stops_polling_and_shows_confirmation(): void
    {
        [$order, $token] = $this->orderWithAccess('paid', 'completed');

        $this->withSession([
            'status' => 'We are checking your payment status. Vouchers are issued after payment confirmation.',
        ])->get(route('voucher.order.thank-you', [
            'orderNumber' => $order->order_number,
            'token' => $token,
        ]))
            ->assertOk()
            ->assertDontSee('data-payment-status-poll', false)
            ->assertDontSee('We are checking your payment status.')
            ->assertSee('Payment confirmed. Your voucher is ready.');
    }

    private function orderWithAccess(string $paymentStatus, string $orderStatus): array
    {
        $token = 'order-status-access-token';
        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-' . fake()->unique()->numerify('########'),
            'access_token_hash' => hash('sha256', $token),
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'guest@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 1000000,
            'total_amount' => 1000000,
            'payment_status' => $paymentStatus,
            'order_status' => $orderStatus,
        ]);

        return [$order, $token];
    }
}
