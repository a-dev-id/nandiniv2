<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\GuestReview;
use App\Models\HomepageSetting;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionImage;
use App\Models\User;
use App\Observers\HomepageCacheObserver;
use App\Services\Affiliate\Booking\AffiliateBookingAnalyticsService;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use App\Services\Payments\Flywire\FlywirePaymentGateway;
use App\Support\WebsiteContentAccess;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(function ($user, string $ability, array $arguments) {
            if ($user instanceof User && WebsiteContentAccess::isEditor($user)) {
                return WebsiteContentAccess::allows($user, $arguments[0] ?? null);
            }

            return null;
        });

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
            HomepageSetting::class,
        ] as $model) {
            $model::observe(HomepageCacheObserver::class);
        }
    }
}
