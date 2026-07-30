<?php

namespace Tests\Feature;

use App\Models\VoucherOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FlywireCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_prefills_booking_reference_and_skips_completed_recipient_step(): void
    {
        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
            'services.flywire.enabled' => true,
            'services.flywire.integration' => 'checkout',
            'services.flywire.environment' => 'demo',
            'services.flywire.recipient_code' => 'FLW',
            'services.flywire.sandbox_payer_middle_name' => 'SANDBOX_TO_DELIVERED_STATUS',
        ]);

        $accessToken = 'checkout-access-token';
        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-260718-TEST',
            'access_token_hash' => hash('sha256', $accessToken),
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'guest@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 1000000,
            'total_amount' => 1000000,
            'payment_status' => 'pending',
            'order_status' => 'pending_payment',
        ]);

        $response = $this
            ->withSession(['voucher.order_access.'.$order->order_number => $accessToken])
            ->get('http://voucher.nandinibali.test/payment/start/'.$order->order_number);

        $response->assertOk()
            ->assertViewHas('configuration', function (array $configuration) use ($order): bool {
                return $configuration['recipientFields'] === [
                    'booking_reference' => $order->order_number,
                ]
                    && $configuration['readonlyFields'] === ['booking_reference']
                    && $configuration['firstName'] === 'Test'
                    && $configuration['middleName'] === 'SANDBOX_TO_DELIVERED_STATUS'
                    && $configuration['lastName'] === 'Guest'
                    && $configuration['requestRecipientInfo'] === true
                    && $configuration['skipCompletedSteps'] === true;
            });
    }

    public function test_checkout_uses_real_payer_names_in_production(): void
    {
        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
            'services.flywire.enabled' => true,
            'services.flywire.integration' => 'checkout',
            'services.flywire.environment' => 'production',
            'services.flywire.recipient_code' => 'FLW',
            'services.flywire.sandbox_payer_middle_name' => 'SANDBOX_TO_DELIVERED_STATUS',
        ]);

        $accessToken = 'production-checkout-access-token';
        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-260718-PROD',
            'access_token_hash' => hash('sha256', $accessToken),
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'guest@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 1000000,
            'total_amount' => 1000000,
            'payment_status' => 'pending',
            'order_status' => 'pending_payment',
        ]);

        $response = $this
            ->withSession(['voucher.order_access.'.$order->order_number => $accessToken])
            ->get('http://voucher.nandinibali.test/payment/start/'.$order->order_number);

        $response->assertOk()
            ->assertViewHas('configuration', fn (array $configuration): bool => $configuration['firstName'] === 'Test'
                && $configuration['lastName'] === 'Guest'
                && ! array_key_exists('middleName', $configuration));
    }

    public function test_check_now_reconciles_a_guaranteed_production_payment(): void
    {
        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
            'services.flywire.enabled' => true,
            'services.flywire.api_key' => 'production-api-key',
            'services.flywire.base_url' => 'https://api-platform.flywire.com/payments/v1',
            'services.flywire.issue_on_statuses' => 'guaranteed',
        ]);
        Http::fake([
            'https://api-platform.flywire.com/payments/v1/payments/FW-PRODUCTION-1' => Http::response([
                'payment_id' => 'FW-PRODUCTION-1',
                'external_reference' => 'NDN-VCH-260730-PROD',
                'status' => 'guaranteed',
            ]),
        ]);

        $accessToken = 'production-reconciliation-token';
        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-260730-PROD',
            'access_token_hash' => hash('sha256', $accessToken),
            'purchaser_first_name' => 'Production',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'production@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 121,
            'total_amount' => 121,
            'payment_status' => 'processing',
            'order_status' => 'pending_payment',
            'flywire_payment_id' => 'FW-PRODUCTION-1',
            'flywire_payment_reference' => 'FW-PRODUCTION-1',
        ]);

        $this
            ->withSession(['voucher.order_access.'.$order->order_number => $accessToken])
            ->post('http://voucher.nandinibali.test/order/'.$order->order_number.'/check-payment')
            ->assertRedirect();

        $this->assertDatabaseHas('voucher_orders', [
            'order_number' => $order->order_number,
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'flywire_status' => 'guaranteed',
        ]);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://api-platform.flywire.com/payments/v1/payments/FW-PRODUCTION-1'
            && $request->hasHeader('X-Authentication-Key', 'production-api-key'));
    }
}
