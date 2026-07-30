<?php

namespace Tests\Feature;

use App\Models\Voucher;
use App\Models\VoucherOrder;
use App\Models\VoucherPaymentEvent;
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

    public function test_version_two_notification_uses_event_type_as_payment_status(): void
    {
        config([
            'services.flywire.shared_secret' => 'secret',
            'services.flywire.issue_on_statuses' => 'guaranteed',
        ]);

        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-V2',
            'purchaser_first_name' => 'Demo',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'demo@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 1000000,
            'total_amount' => 1000000,
            'payment_status' => 'processing',
            'order_status' => 'pending_payment',
        ]);

        $payload = json_encode([
            'event_type' => 'guaranteed',
            'event_date' => now()->toIso8601ZuluString(),
            'event_resource' => 'payments',
            'data' => [
                'payment_id' => 'FW-DEMO-1',
                'external_reference' => $order->order_number,
            ],
        ], JSON_THROW_ON_ERROR);
        $digest = base64_encode(hash_hmac('sha256', $payload, 'secret', true));

        $this->call('POST', '/api/flywire/notifications', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_FLYWIRE_DIGEST' => $digest,
        ], $payload)->assertOk();

        $this->assertDatabaseHas('voucher_orders', [
            'order_number' => $order->order_number,
            'flywire_payment_id' => 'FW-DEMO-1',
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'flywire_status' => 'guaranteed',
        ]);
        $this->assertDatabaseHas('voucher_payment_events', [
            'gateway_payment_id' => 'FW-DEMO-1',
            'event_type' => 'guaranteed',
            'gateway_status' => 'guaranteed',
        ]);
    }

    public function test_replayed_version_two_notification_repairs_a_previously_misread_guaranteed_event(): void
    {
        config([
            'services.flywire.shared_secret' => 'secret',
            'services.flywire.issue_on_statuses' => 'guaranteed',
        ]);

        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-V2-REPLAY',
            'purchaser_first_name' => 'Production',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'production@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 121,
            'total_amount' => 121,
            'payment_status' => 'processing',
            'order_status' => 'pending_payment',
        ]);

        $payload = json_encode([
            'event_type' => 'guaranteed',
            'event_date' => now()->toIso8601ZuluString(),
            'event_resource' => 'payments',
            'data' => [
                'payment_id' => 'FW-PRODUCTION-1',
                'external_reference' => $order->order_number,
            ],
        ], JSON_THROW_ON_ERROR);
        $fingerprint = hash('sha256', implode('|', ['', '', 'FW-PRODUCTION-1', $payload]));

        VoucherPaymentEvent::query()->create([
            'voucher_order_id' => $order->id,
            'gateway' => 'flywire',
            'gateway_payment_id' => 'FW-PRODUCTION-1',
            'event_fingerprint' => $fingerprint,
            'event_type' => 'guaranteed',
            'gateway_status' => 'unknown',
            'signature_valid' => true,
            'payload' => json_decode($payload, true, flags: JSON_THROW_ON_ERROR),
            'processed_at' => now(),
        ]);

        $digest = base64_encode(hash_hmac('sha256', $payload, 'secret', true));

        $this->call('POST', '/api/flywire/notifications', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_FLYWIRE_DIGEST' => $digest,
        ], $payload)->assertOk();

        $this->assertDatabaseHas('voucher_orders', [
            'order_number' => $order->order_number,
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'flywire_status' => 'guaranteed',
        ]);
        $this->assertDatabaseHas('voucher_payment_events', [
            'id' => VoucherPaymentEvent::query()->value('id'),
            'gateway_status' => 'guaranteed',
            'processing_error' => null,
        ]);
    }
}
