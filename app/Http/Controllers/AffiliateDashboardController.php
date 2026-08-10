<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProgramSetting;
use App\Models\BookingSyncLog;
use App\Services\Affiliate\AffiliateBookingUrlBuilder;
use App\Services\Affiliate\AffiliateLinkService;
use App\Services\Affiliate\Booking\AffiliateBookingAnalyticsService;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use App\Services\Affiliate\Finance\AffiliateFinanceAnalyticsService;
use App\Services\Affiliate\Finance\AffiliateCurrencyConverter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AffiliateDashboardController extends Controller
{
    public function __invoke(
        Request $request,
        AffiliateBookingUrlBuilder $bookingUrls,
        AffiliateLinkService $links,
        AffiliateClickAnalyticsService $analytics,
        AffiliateBookingAnalyticsService $bookingAnalytics,
        AffiliateMoneyFormatter $money,
        AffiliateFinanceAnalyticsService $financeAnalytics,
        AffiliateCurrencyConverter $currencyConverter,
    ): View {
        $affiliate = Auth::guard('affiliate')->user();

        Gate::forUser($affiliate)->authorize('view', $affiliate);

        $showPendingReviewModal = false;
        $showApprovedWelcomeModal = false;

        if ($affiliate->isPending() && ! session()->has('affiliate.pending-review-notice-shown')) {
            session()->put('affiliate.pending-review-notice-shown', true);
            $showPendingReviewModal = true;
        }

        if ($affiliate->isApproved()
            && $affiliate->dashboard_welcome_dismissed_at === null
            && ! session()->has('affiliate.approved-welcome-notice-shown')) {
            session()->put('affiliate.approved-welcome-notice-shown', true);
            $showApprovedWelcomeModal = true;
        }

        $hasActiveTools = $affiliate->isApproved()
            && filled($affiliate->affiliate_code)
            && filled($affiliate->short_link_slug)
            && $affiliate->short_link_activated_at !== null;

        $validated = $request->validate([
            'range' => ['nullable', Rule::in(AffiliateClickAnalyticsService::RANGES)],
            'bookings' => ['nullable', Rule::in(AffiliateBookingAnalyticsService::FILTERS)],
        ]);
        $range = $validated['range'] ?? '30';
        $bookingFilter = $validated['bookings'] ?? 'upcoming';
        $bookingSyncMaximumAgeHours = max(1, (int) config('services.membership_api.booking_sync_max_age_hours', 25));
        $lastSuccessfulBookingSync = BookingSyncLog::query()
            ->where('status', BookingSyncLog::STATUS_SUCCESS)
            ->latest('finished_at')
            ->first()?->finished_at;

        return view('pages.affiliate.dashboard', [
            'affiliate' => $affiliate,
            'settings' => AffiliateProgramSetting::current(),
            'showPendingReviewModal' => $showPendingReviewModal,
            'showApprovedWelcomeModal' => $showApprovedWelcomeModal,
            'hasActiveTools' => $hasActiveTools,
            'shortLink' => $hasActiveTools ? $links->shortLink($affiliate) : null,
            'bookingLink' => $hasActiveTools ? $bookingUrls->build($affiliate->affiliate_code) : null,
            'analyticsRange' => $range,
            'analytics' => $affiliate->isApproved() ? $analytics->forAffiliate($affiliate, $range) : null,
            'bookingFilter' => $bookingFilter,
            'bookingAnalytics' => $affiliate->isApproved() ? $bookingAnalytics->forAffiliate($affiliate, $bookingFilter) : null,
            'bookingDataMayBeStale' => $affiliate->isApproved() && ($lastSuccessfulBookingSync === null || $lastSuccessfulBookingSync->lt(now()->subHours($bookingSyncMaximumAgeHours))),
            'money' => $money,
            'finance' => $affiliate->isApproved() ? $financeAnalytics->forAffiliate($affiliate) : null,
            'currencyConverter' => $currencyConverter,
        ]);
    }
}
