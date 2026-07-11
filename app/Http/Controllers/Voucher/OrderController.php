<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Models\VoucherOrder;
use Illuminate\View\View;

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

    private function render(string $orderNumber): View
    {
        $order = VoucherOrder::query()
            ->with(['items.issuedVouchers'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $member = auth('member')->user();
        $token = request('token');
        $sessionToken = session('voucher.order_access.' . $order->order_number);
        $submittedToken = $token ?: $sessionToken;
        $tokenIsValid = $submittedToken && $order->access_token_hash && hash_equals($order->access_token_hash, hash('sha256', $submittedToken));

        abort_unless($member?->id === $order->member_id || $tokenIsValid, 403);

        return view('voucher.order', ['order' => $order]);
    }
}
