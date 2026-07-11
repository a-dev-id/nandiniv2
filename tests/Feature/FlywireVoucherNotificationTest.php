<?php

namespace Tests\Feature;

use App\Models\Voucher;
use App\Models\VoucherOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlywireVoucherNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_configured_success_notification_issues_expected_vouchers_once(): void
    {
        config([
            'services.flywire.shared_secret' => 'secret',
            'services.flywire.issue_on_statuses' => 'guaranteed',
            'domains.voucher' => 'voucher.nandinibali.test',
        ]);

        $voucher = Voucher::factory()->create([
            'voucher_type' => 'spa',
            'face_value' => 3000000,
        ]);

        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-TEST',
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'guest@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 3000000,
            'total_amount' => 3000000,
            'payment_status' => 'processing',
            'order_status' => 'pending_payment',
        ]);

        $order->items()->create([
            'voucher_id' => $voucher->id,
            'voucher_title' => $voucher->title,
            'voucher_type' => $voucher->voucher_type,
            'quantity' => 3,
            'unit_price' => 1000000,
            'line_total' => 3000000,
            'currency' => 'IDR',
            'recipient_name' => 'Recipient',
            'recipient_email' => 'recipient@example.com',
            'voucher_snapshot' => [
                'face_value' => 3000000,
                'validity_type' => 'days_after_issue',
                'validity_days' => 365,
            ],
        ]);

        $payload = json_encode([
            'id' => 'evt_1',
            'external_reference' => $order->order_number,
            'status' => 'guaranteed',
            'payment' => [
                'id' => 'pay_1',
                'reference' => 'FW-1',
                'status' => 'guaranteed',
            ],
        ], JSON_THROW_ON_ERROR);

        $digest = base64_encode(hash_hmac('sha256', $payload, 'secret', true));

        $this->call('POST', '/api/flywire/notifications', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_FLYWIRE_DIGEST' => $digest,
        ], $payload)->assertOk();

        $this->call('POST', '/api/flywire/notifications', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_FLYWIRE_DIGEST' => $digest,
        ], $payload)->assertOk();

        $this->assertDatabaseCount('issued_vouchers', 3);
        $this->assertDatabaseHas('voucher_orders', [
            'order_number' => $order->order_number,
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
    }

    public function test_invalid_digest_is_rejected(): void
    {
        config(['services.flywire.shared_secret' => 'secret']);

        $this->call('POST', '/api/flywire/notifications', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_FLYWIRE_DIGEST' => 'invalid',
        ], '{"id":"evt"}')->assertForbidden();
    }
}
