<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\GuestReview;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionImage;
use App\Observers\HomepageCacheObserver;
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

        foreach ([
            Page::class,
            PageSection::class,
            PageSectionImage::class,
            Offer::class,
            Accommodation::class,
            AccommodationImage::class,
            ExperienceCategory::class,
            Experience::class,
            GuestReview::class,
        ] as $model) {
            $model::observe(HomepageCacheObserver::class);
        }
    }
}
