<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAffiliatePaymentProfileRequest;
use App\Models\Permission;
use App\Services\Affiliate\Finance\AffiliatePaymentProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AffiliatePaymentProfileController extends Controller
{
    public function edit(): View
    {
        $affiliate = Auth::guard('affiliate')->user();
        abort_unless($affiliate->isApproved() && $affiliate->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_UPDATE_OWN), 403);

        return view('pages.affiliate.payment-details', [
            'affiliate' => $affiliate,
            'profile' => $affiliate->paymentProfile,
        ]);
    }

    public function update(UpdateAffiliatePaymentProfileRequest $request, AffiliatePaymentProfileService $service): RedirectResponse
    {
        $affiliate = Auth::guard('affiliate')->user();
        abort_unless($affiliate->isApproved() && $affiliate->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_UPDATE_OWN), 403);
        $service->updateOwn($affiliate, $request->validated());

        return redirect()->route('affiliate.payment-details.edit')->with('status', 'Payment details saved securely. Finance will review the updated information.');
    }
}
