<?php

namespace Tests\Feature;

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
use Tests\TestCase;

class AffiliateAdminNavigationTest extends TestCase
{
    public function test_core_affiliate_management_items_are_registered_in_navigation(): void
    {
        $this->assertTrue(AffiliateResource::shouldRegisterNavigation());
        $this->assertSame('Affiliates', AffiliateResource::getNavigationLabel());

        $this->assertTrue(AffiliateBookingResource::shouldRegisterNavigation());
        $this->assertSame('Affiliate Bookings', AffiliateBookingResource::getNavigationLabel());
    }

    public function test_part_six_operational_tools_are_registered_in_their_final_navigation_groups(): void
    {
        foreach ([AffiliateMarketingAssetResource::class, AffiliateOperationalReports::class, AffiliateClickAnalytics::class] as $page) {
            $this->assertTrue($page::shouldRegisterNavigation());
            $this->assertSame('Affiliate Management', $page::getNavigationGroup());
        }

        foreach ([AffiliateFinanceOverview::class, AffiliateCommissionPeriodResource::class, AffiliateCommissionItemResource::class, AffiliatePaymentProfileResource::class, AffiliatePayoutResource::class, AffiliatePayoutMinimumResource::class] as $page) {
            $this->assertTrue($page::shouldRegisterNavigation());
            $this->assertSame('Affiliate Finance', $page::getNavigationGroup());
        }

        foreach ([AffiliateSystemHealth::class, AffiliateProgramSettingResource::class] as $page) {
            $this->assertTrue($page::shouldRegisterNavigation());
            $this->assertSame('Affiliate System', $page::getNavigationGroup());
        }
    }
}
