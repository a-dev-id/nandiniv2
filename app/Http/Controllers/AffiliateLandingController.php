<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProgramSetting;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AffiliateLandingController extends Controller
{
    public function __invoke(): View
    {
        $settings = AffiliateProgramSetting::current();
        $page = Page::query()
            ->whereKey(44)
            ->where('is_active', true)
            ->first();
        $affiliate = Auth::guard('affiliate')->user();
        $registrationEnabled = (bool) config('features.affiliate_registration_enabled');
        $formatPercentage = static fn (string $value): string => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');

        return view('pages.affiliate.landing', [
            'settings' => $settings,
            'page' => $page,
            'affiliate' => $affiliate,
            'primaryCtaLabel' => $affiliate ? 'Go to Dashboard' : ($registrationEnabled ? 'Join Affiliate' : 'Registration Temporarily Unavailable'),
            'primaryCtaUrl' => $affiliate ? route('affiliate.dashboard') : ($registrationEnabled ? route('affiliate.register') : null),
            'showLoginCta' => $affiliate === null,
            'registrationEnabled' => $registrationEnabled,
            'commissionPercentage' => $formatPercentage($settings->affiliate_commission_percentage),
            'guestDiscountPercentage' => $formatPercentage($settings->guest_discount_percentage),
        ]);
    }
}
