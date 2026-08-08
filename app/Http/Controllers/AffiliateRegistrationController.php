<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Http\Requests\StoreAffiliateRegistrationRequest;
use App\Services\Affiliate\CreateAffiliateService;
use App\Services\Affiliate\AffiliateEmailVerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AffiliateRegistrationController extends Controller
{
    public function create(): View
    {
        return view('pages.affiliate.auth.register');
    }

    public function store(
        StoreAffiliateRegistrationRequest $request,
        CreateAffiliateService $service,
        AffiliateEmailVerificationService $verification,
    ): RedirectResponse
    {
        $affiliate = $service->create(
            $request->safe()->except(['password', 'password_confirmation', 'g-recaptcha-response']),
            AffiliateRegistrationSource::SelfRegistration,
            AffiliateStatus::Pending,
            password: $request->string('password')->toString(),
        );

        $verification->send($affiliate);

        Auth::guard('member')->logout();
        Auth::guard('affiliate')->login($affiliate);
        $request->session()->regenerate();
        $request->session()->forget('affiliate.pending-review-notice-shown');

        return redirect()->route('affiliate.dashboard');
    }
}
