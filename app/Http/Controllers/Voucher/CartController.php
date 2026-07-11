<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\AddVoucherToCartRequest;
use App\Models\Voucher;
use App\Services\Voucher\Cart\VoucherCartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(VoucherCartService $cart): View
    {
        return view('voucher.cart', ['cart' => $cart->refresh()]);
    }

    public function add(AddVoucherToCartRequest $request, Voucher $voucher, VoucherCartService $cart): RedirectResponse
    {
        try {
            $cart->add($voucher, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['voucher' => $e->getMessage()])->withInput();
        }

        return redirect()->route('voucher.cart.index')->with('status', 'Voucher added to your cart.');
    }

    public function update(string $key, Request $request, VoucherCartService $cart): RedirectResponse
    {
        $data = $request->validate((new AddVoucherToCartRequest())->rules());

        try {
            $cart->update($key, $data);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        return back()->with('status', 'Cart updated.');
    }

    public function remove(string $key, VoucherCartService $cart): RedirectResponse
    {
        $cart->remove($key);

        return back()->with('status', 'Voucher removed from your cart.');
    }
}
