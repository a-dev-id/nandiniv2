<?php

namespace Tests\Feature;

use App\Models\VoucherOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->withSession(['voucher.order_access.' . $order->order_number => $accessToken])
            ->get('http://voucher.nandinibali.test/payment/start/' . $order->order_number);

        $response->assertOk()
            ->assertViewHas('configuration', function (array $configuration) use ($order): bool {
                return $configuration['recipientFields'] === [
                    'booking_reference' => $order->order_number,
                ]
                    && $configuration['requestRecipientInfo'] === true
                    && $configuration['skipCompletedSteps'] === true;
            });
    }
}
