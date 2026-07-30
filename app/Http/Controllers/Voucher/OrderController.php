<?php

namespace App\Http\Controllers\Voucher;

use App\Contracts\Payments\PaymentGateway;
use App\Http\Controllers\Controller;
use App\Models\VoucherOrder;
use App\Services\Payments\Flywire\FlywireStatusMapper;
use App\Services\Voucher\VoucherIssuer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function show(string $orderNumber): View
    {
        return $this->render($orderNumber);
    }

    public function thankYou(string $orderNumber): View
    {
        return $this->render($orderNumber);
    }

    public function checkPayment(
        string $orderNumber,
        PaymentGateway $gateway,
        FlywireStatusMapper $mapper,
        VoucherIssuer $issuer,
    ): RedirectResponse {
        $order = $this->findAuthorizedOrder($orderNumber);
        $paymentId = $order->flywire_payment_id ?: $order->flywire_payment_reference;

        if (in_array($order->payment_status, ['paid', 'failed', 'cancelled'], true)) {
            return $this->redirectToOrder($order);
        }

        if (blank($paymentId)) {
            return $this->redirectToOrder($order)
                ->with('status', 'The Flywire payment reference is not available yet. Please try again shortly.');
        }

        try {
            $payment = $gateway->retrievePayment((string) $paymentId);
        } catch (RuntimeException $exception) {
            report($exception);

            return $this->redirectToOrder($order)
                ->with('status', 'We could not refresh the Flywire payment status. Please try again shortly.');
        }

        $externalReference = data_get($payment->raw, 'external_reference');

        if (filled($externalReference) && ! hash_equals($order->order_number, (string) $externalReference)) {
            report(new RuntimeException('Flywire payment reference did not match the voucher order.'));

            return $this->redirectToOrder($order)
                ->with('status', 'We could not match this Flywire payment to the order. Please contact us for assistance.');
        }

        DB::transaction(function () use ($order, $payment, $mapper): void {
            $lockedOrder = VoucherOrder::query()->lockForUpdate()->findOrFail($order->id);
            $lockedOrder->forceFill([
                'payment_status' => $mapper->paymentStatus($payment->status),
                'order_status' => $mapper->shouldIssue($payment->status) ? 'processing' : $lockedOrder->order_status,
                'flywire_payment_id' => $payment->paymentId ?: $lockedOrder->flywire_payment_id,
                'flywire_payment_reference' => $payment->paymentId ?: $lockedOrder->flywire_payment_reference,
                'flywire_status' => $payment->status,
            ])->save();
        });

        if ($mapper->shouldIssue($payment->status)) {
            $issuer->issueForOrder($order);
        }

        return $this->redirectToOrder($order->fresh())
            ->with('status', $mapper->shouldIssue($payment->status)
                ? 'Payment confirmed. Your voucher is ready.'
                : 'Flywire is still processing your payment.');
    }

    private function render(string $orderNumber): View
    {
        $order = $this->findAuthorizedOrder($orderNumber, ['items.issuedVouchers']);

        return view('voucher.order', ['order' => $order]);
    }

    /**
     * @param  array<int, string>  $with
     */
    private function findAuthorizedOrder(string $orderNumber, array $with = []): VoucherOrder
    {
        $order = VoucherOrder::query()
            ->with($with)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $member = auth('member')->user();
        $token = request('token');
        $sessionToken = session('voucher.order_access.'.$order->order_number);
        $submittedToken = $token ?: $sessionToken;
        $tokenIsValid = $submittedToken && $order->access_token_hash && hash_equals($order->access_token_hash, hash('sha256', $submittedToken));

        abort_unless($member?->id === $order->member_id || $tokenIsValid, 403);

        return $order;
    }

    private function redirectToOrder(VoucherOrder $order): RedirectResponse
    {
        return redirect()->route('voucher.order.thank-you', array_filter([
            'orderNumber' => $order->order_number,
            'token' => request('token'),
        ]));
    }
}
