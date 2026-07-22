<?php

namespace App\Services\Payments\Flywire;

use App\Contracts\Payments\PaymentGateway;
use App\Data\Payments\PaymentResult;
use App\Data\Payments\PaymentSessionResult;
use App\Models\VoucherOrder;
use RuntimeException;

class FlywirePaymentGateway implements PaymentGateway
{
    public function __construct(private readonly FlywireClient $client)
    {
    }

    public function createCheckoutSession(VoucherOrder $order): PaymentSessionResult
    {
        if (! (bool) config('services.flywire.enabled')) {
            return new PaymentSessionResult(
                sessionId: 'local-disabled-' . $order->order_number,
                redirectUrl: route('voucher.order.thank-you', $order->order_number),
                raw: ['disabled' => true],
            );
        }

        if (config('services.flywire.integration', 'checkout') === 'checkout') {
            $recipientCode = trim((string) config('services.flywire.recipient_code'));

            if ($recipientCode === '') {
                throw new RuntimeException('The payment service is not configured.');
            }

            return new PaymentSessionResult(
                sessionId: 'checkout-' . $order->order_number,
                redirectUrl: null,
                raw: [
                    'integration' => 'checkout',
                    'environment' => $this->checkoutEnvironment(),
                    'recipient_code' => $recipientCode,
                ],
            );
        }

        $payload = [
            'recipient_id' => config('services.flywire.recipient_id'),
            'external_reference' => $order->order_number,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'payer' => array_filter([
                'first_name' => $order->purchaser_first_name,
                'middle_name' => $this->checkoutEnvironment() === 'demo'
                    ? config('services.flywire.sandbox_payer_middle_name')
                    : null,
                'last_name' => $order->purchaser_last_name,
                'email' => $order->purchaser_email,
                'phone' => $order->purchaser_phone,
                'country' => $order->billing_country_code,
            ], fn ($value): bool => $value !== null && $value !== ''),
            'notification_url' => config('services.flywire.notification_url'),
            'return_url' => route('voucher.payment.return', ['order' => $order->order_number]),
            'cancel_url' => config('services.flywire.cancel_url'),
        ];

        $response = $this->client->post('/checkout/sessions', $payload);

        return new PaymentSessionResult(
            sessionId: (string) data_get($response, 'id', data_get($response, 'session_id', $order->order_number)),
            redirectUrl: data_get($response, 'hosted_form_url', data_get($response, 'url')),
            raw: $response,
        );
    }

    public function retrievePayment(string $paymentId): PaymentResult
    {
        $response = $this->client->get('/payments/' . rawurlencode($paymentId));

        return new PaymentResult(
            paymentId: (string) data_get($response, 'id', $paymentId),
            status: (string) data_get($response, 'status', 'unknown'),
            raw: $response,
        );
    }

    public function expireCheckoutSession(string $sessionId): void
    {
        if ((bool) config('services.flywire.enabled')) {
            $this->client->post('/checkout/sessions/' . rawurlencode($sessionId) . '/expire', []);
        }
    }

    private function checkoutEnvironment(): string
    {
        return in_array(config('services.flywire.environment'), ['prod', 'production'], true)
            ? 'prod'
            : 'demo';
    }
}
