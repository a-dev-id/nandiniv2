<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictFilamentAffiliateStaffAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->hasRole(Role::ADMINISTRATOR)) {
            return $next($request);
        }

        if ($request->routeIs('filament.admin.pages.dashboard')) {
            if ($user?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW)) {
                return redirect()->route('filament.admin.resources.affiliate-commission-periods.index');
            }

            return $user?->hasPermissionTo(Permission::AFFILIATE_VIEW)
                ? redirect()->route('filament.admin.resources.affiliates.index')
                : redirect()->route('filament.admin.resources.affiliate-bookings.index');
        }

        if ($request->routeIs('filament.admin.resources.affiliate-bookings.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW), 403);

            return $next($request);
        }

        if ($request->routeIs('filament.admin.resources.affiliate-commission-periods.*', 'filament.admin.resources.affiliate-commission-items.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW), 403);

            return $next($request);
        }

        if ($request->routeIs('filament.admin.resources.affiliate-payment-profiles.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_VIEW), 403);

            return $next($request);
        }

        if ($request->routeIs('filament.admin.resources.affiliate-payouts.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_PAYOUT_VIEW), 403);

            return $next($request);
        }

        if ($request->routeIs('filament.admin.resources.affiliate-payout-minimums.*', 'filament.admin.resources.affiliate-program-settings.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_SETTING_MANAGE), 403);

            return $next($request);
        }

        if ($request->routeIs('filament.admin.resources.affiliate-marketing-assets.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_MARKETING_ASSET_MANAGE), 403);

            return $next($request);
        }

        if ($request->routeIs('filament.admin.resources.affiliates.*')) {
            abort_unless($user?->hasPermissionTo(Permission::AFFILIATE_VIEW), 403);

            return $next($request);
        }

        $mayViewClickAnalytics = $user?->hasPermissionTo(Permission::AFFILIATE_CLICK_VIEW)
            && $request->routeIs('filament.admin.pages.affiliate-click-analytics');

        $mayViewFinanceOverview = $user?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW)
            && $request->routeIs('filament.admin.pages.affiliate-finance-overview');

        $mayViewReports = $user?->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW)
            && $request->routeIs('filament.admin.pages.affiliate-operational-reports');

        $mayViewSystemHealth = $user?->hasPermissionTo(Permission::AFFILIATE_SYSTEM_HEALTH_VIEW)
            && $request->routeIs('filament.admin.pages.affiliate-system-health');

        abort_unless($mayViewClickAnalytics || $mayViewFinanceOverview || $mayViewReports || $mayViewSystemHealth, 403);

        return $next($request);
    }
}
