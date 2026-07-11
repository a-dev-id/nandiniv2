<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use App\Services\Voucher\Cart\VoucherCartService;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(VoucherCartService $cart): View
    {
        $landingPage = Page::query()
            ->whereKey(config('domains.voucher_landing_page_id'))
            ->where('is_active', true)
            ->first();

        return view('voucher.index', [
            'landingPage' => $landingPage,
            'categories' => VoucherCategory::query()
                ->active()
                ->whereHas('vouchers', fn($query) => $query->active())
                ->withCount(['vouchers as active_vouchers_count' => fn($query) => $query->active()])
                ->ordered()
                ->get(),
            'featuredVouchers' => Voucher::query()->with('category')->active()->featured()->ordered()->limit(6)->get(),
            'cartCount' => $cart->countUnits(),
        ]);
    }

    public function category(VoucherCategory $voucherCategory, VoucherCartService $cart): View
    {
        abort_unless($voucherCategory->is_active, 404);

        return view('voucher.category', [
            'category' => $voucherCategory,
            'vouchers' => $voucherCategory->vouchers()->with('category')->active()->ordered()->get(),
            'cartCount' => $cart->countUnits(),
        ]);
    }

    public function show(Voucher $voucher, VoucherCartService $cart): View
    {
        abort_unless($voucher->purchasable, 404);

        return view('voucher.show', [
            'voucher' => $voucher->load('category'),
            'relatedVouchers' => Voucher::query()
                ->with('category')
                ->active()
                ->whereKeyNot($voucher->id)
                ->when($voucher->voucher_category_id, fn($query) => $query->where('voucher_category_id', $voucher->voucher_category_id))
                ->ordered()
                ->limit(3)
                ->get(),
            'cartCount' => $cart->countUnits(),
        ]);
    }
}
