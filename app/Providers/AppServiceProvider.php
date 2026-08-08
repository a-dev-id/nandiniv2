<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Services\Affiliate\Booking\AffiliateBookingAnalyticsService;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use App\Services\Payments\Flywire\FlywirePaymentGateway;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, FlywirePaymentGateway::class);
        $this->app->scoped(AffiliateClickAnalyticsService::class);
        $this->app->scoped(AffiliateBookingAnalyticsService::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
