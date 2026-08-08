<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AffiliateClickAnalytics;
use App\Filament\Pages\AffiliateFinanceOverview;
use App\Filament\Pages\AffiliateOperationalReports;
use App\Filament\Pages\AffiliateSystemHealth;
use App\Filament\Resources\AffiliateBookings\AffiliateBookingResource;
use App\Filament\Resources\AffiliateCommissionItems\AffiliateCommissionItemResource;
use App\Filament\Resources\AffiliateCommissionPeriods\AffiliateCommissionPeriodResource;
use App\Filament\Resources\AffiliateMarketingAssets\AffiliateMarketingAssetResource;
use App\Filament\Resources\AffiliatePaymentProfiles\AffiliatePaymentProfileResource;
use App\Filament\Resources\AffiliatePayoutMinimums\AffiliatePayoutMinimumResource;
use App\Filament\Resources\AffiliatePayouts\AffiliatePayoutResource;
use App\Filament\Resources\AffiliateProgramSettings\AffiliateProgramSettingResource;
use App\Filament\Resources\Affiliates\AffiliateResource;
use App\Filament\Widgets\BookingSyncOverview;
use App\Filament\Widgets\MembershipOverview;
use App\Filament\Widgets\VoucherOverview;
use App\Http\Middleware\RestrictFilamentAffiliateStaffAccess;
use App\Models\Permission;
use App\Models\Role;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('panel/admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                MembershipOverview::class,
                BookingSyncOverview::class,
                VoucherOverview::class,
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->navigation(function (): NavigationBuilder|bool {
                $user = auth('web')->user();

                if (! $user || $user->hasRole(Role::ADMINISTRATOR)) {
                    return true;
                }

                return (new NavigationBuilder)
                    ->group('Affiliate Management', [
                        ...($user->hasPermissionTo(Permission::AFFILIATE_VIEW) ? AffiliateResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW) ? AffiliateBookingResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_CLICK_VIEW) ? AffiliateClickAnalytics::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_MARKETING_ASSET_MANAGE) ? AffiliateMarketingAssetResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW) ? AffiliateOperationalReports::getNavigationItems() : []),
                    ])
                    ->group('Affiliate Finance', [
                        ...($user->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW) ? AffiliateFinanceOverview::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW) ? AffiliateCommissionPeriodResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW) ? AffiliateCommissionItemResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_VIEW) ? AffiliatePaymentProfileResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_PAYOUT_VIEW) ? AffiliatePayoutResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_SETTING_MANAGE) ? AffiliatePayoutMinimumResource::getNavigationItems() : []),
                    ])
                    ->group('Affiliate System', [
                        ...($user->hasPermissionTo(Permission::AFFILIATE_SETTING_MANAGE) ? AffiliateProgramSettingResource::getNavigationItems() : []),
                        ...($user->hasPermissionTo(Permission::AFFILIATE_SYSTEM_HEALTH_VIEW) ? AffiliateSystemHealth::getNavigationItems() : []),
                    ]);
            })
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RestrictFilamentAffiliateStaffAccess::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Guest Operations'),

                NavigationGroup::make()
                    ->label('Affiliate Management'),

                NavigationGroup::make()
                    ->label('Affiliate Finance'),

                NavigationGroup::make()
                    ->label('Affiliate System'),

                NavigationGroup::make()
                    ->label('Membership'),

                NavigationGroup::make()
                    ->label('Vouchers'),

                NavigationGroup::make()
                    ->label('Website Content'),

                NavigationGroup::make()
                    ->label('Settings'),
            ]);
    }
}
