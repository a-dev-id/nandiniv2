<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Models\VoucherOrder;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function start(VoucherOrder $order): RedirectResponse
    {
        if ($order->flywire_hosted_form_url) {
            return redirect()->away($order->flywire_hosted_form_url);
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
                ->with('status', 'We are checking your payment status. Vouchers are issued after Flywire confirmation.');
        }

        return redirect()->route('voucher.index');
    }
}
