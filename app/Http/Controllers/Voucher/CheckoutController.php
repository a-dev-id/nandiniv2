<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\CheckoutVoucherRequest;
use App\Services\Voucher\Cart\VoucherCartService;
use App\Services\Voucher\VoucherCheckoutService;
use App\Support\InquiryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(VoucherCartService $cart): View|RedirectResponse
    {
        $cartData = $cart->refresh();

        if ($cartData['lines']->isEmpty()) {
            return redirect()->route('voucher.cart.index')->withErrors(['cart' => 'Your voucher cart is empty.']);
        }

        $member = auth('member')->user();
        $selfLine = $cartData['lines']->first(fn(array $line): bool => ($line['purchase_for'] ?? 'gift') === 'self');
        $selfNameParts = $selfLine ? preg_split('/\s+/', trim((string) $selfLine['recipient_name']), 2) : [];
        $countries = InquiryOptions::countryCodes();
        $memberCountry = $member?->country;
        $memberCountryCode = array_key_exists((string) $memberCountry, $countries)
            ? $memberCountry
            : array_search($memberCountry, $countries, true);

        return view('voucher.checkout', [
            'cart' => $cartData,
            'member' => $member,
            'countries' => $countries,
            'purchaserDefaults' => [
                'first_name' => $member?->first_name ?: ($selfNameParts[0] ?? null),
                'last_name' => $member?->last_name ?: ($selfNameParts[1] ?? null),
                'email' => $member?->email ?: ($selfLine['recipient_email'] ?? null),
                'phone' => $member?->phone_number,
                'country' => $memberCountryCode ?: null,
            ],
        ]);
    }

    public function store(CheckoutVoucherRequest $request, VoucherCheckoutService $checkout): RedirectResponse
    {
        try {
            $order = $checkout->createOrderAndPayment($request->validated(), auth('member')->user());
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }

        return redirect()->route('voucher.payment.start', $order->order_number);
    }
}
