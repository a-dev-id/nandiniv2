<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Models\VoucherOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function start(VoucherOrder $order): RedirectResponse|View
    {
        $accessToken = session('voucher.order_access.' . $order->order_number);
        abort_unless(
            filled($accessToken) && hash_equals((string) $order->access_token_hash, hash('sha256', (string) $accessToken)),
            403
        );

        if ($order->flywire_hosted_form_url) {
            return redirect()->away($order->flywire_hosted_form_url);
        }

        if ((bool) config('services.flywire.enabled') && config('services.flywire.integration', 'checkout') === 'checkout') {
            $isProduction = in_array(config('services.flywire.environment'), ['prod', 'production'], true);
            $environment = $isProduction ? 'prod' : 'demo';
            $sandboxPayerMiddleName = $isProduction
                ? null
                : config('services.flywire.sandbox_payer_middle_name');
            $notificationUrl = config('services.flywire.notification_url');
            $notificationHost = parse_url((string) $notificationUrl, PHP_URL_HOST);
            $hasPublicNotificationUrl = filled($notificationUrl)
                && parse_url((string) $notificationUrl, PHP_URL_SCHEME) === 'https'
                && filled($notificationHost)
                && ! in_array($notificationHost, ['localhost', '127.0.0.1'], true)
                && ! str_ends_with((string) $notificationHost, '.test');
            $configuration = array_filter([
                'env' => $environment,
                'recipientCode' => config('services.flywire.recipient_code'),
                'amount' => $order->total_amount,
                'firstName' => $order->purchaser_first_name,
                'middleName' => $sandboxPayerMiddleName,
                'lastName' => $order->purchaser_last_name,
                'email' => $order->purchaser_email,
                'phone' => $order->purchaser_phone,
                'country' => $order->billing_country_code,
                'recipientFields' => [
                    'booking_reference' => $order->order_number,
                ],
                'requestPayerInfo' => true,
                'requestRecipientInfo' => true,
                'skipCompletedSteps' => true,
                'nonce' => $order->order_number,
                'returnUrl' => route('voucher.payment.return', ['order' => $order->order_number]),
                'callbackUrl' => $hasPublicNotificationUrl ? $notificationUrl : null,
                'callbackId' => $hasPublicNotificationUrl ? $order->order_number : null,
                'callbackVersion' => $hasPublicNotificationUrl ? '2' : null,
            ], fn ($value): bool => $value !== null && $value !== '');

            return view('voucher.payment', compact('order', 'configuration'));
        }

        return redirect()->route('voucher.order.thank-you', [
            'orderNumber' => $order->order_number,
            'token' => session('voucher.order_access.' . $order->order_number),
        ]);
    }

    public function return(?string $order = null): RedirectResponse
    {
        $orderNumber = $order ?: request('order') ?: request('external_reference');

        if ($orderNumber) {
            return redirect()->route('voucher.order.thank-you', [
                'orderNumber' => $orderNumber,
                'token' => session('voucher.order_access.' . $orderNumber),
            ])
                ->with('status', 'We are checking your payment status. Vouchers are issued after payment confirmation.');
        }

        return redirect()->route('voucher.index');
    }
}
