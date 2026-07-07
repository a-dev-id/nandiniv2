<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\Cron\BlogNewsPublicationController;
use App\Http\Controllers\WeddingController;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\SustainabilityController;
use App\Http\Controllers\DiningController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HoneymoonController;
use App\Http\Controllers\LittleThingsController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\Cron\BookingSyncController;
use App\Http\Controllers\Cron\MemberCheckoutNotificationController;
use App\Http\Controllers\Cron\MembershipLifecycleController;
use App\Http\Controllers\Cron\OfferPublicationController;
use App\Http\Controllers\Cron\TestWelcomeEmailController;
use App\Http\Controllers\Cron\WebhotelierSyncController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\HolyRiverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\MemberEmailPreviewController;
use App\Http\Controllers\MemberRewardRedemptionController;
use App\Http\Controllers\MembershipAuthController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipProfileController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Services\WebhotelierPullService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/__clear-cache/nandini-2026-06-23', function () {
    Artisan::call('optimize:clear');

    return response('Cache cleared', 200)
        ->header('Content-Type', 'text/plain');
});

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/explore', [PageController::class, 'explore'])
    ->name('explore');

Route::get('/sitemap.xml', SitemapController::class)
    ->name('sitemap');

Route::get('/login', function () {
    if (config('features.disable_membership_feature')) {
        return redirect()->route('home');
    }

    return redirect()->route('membership.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Membership
|--------------------------------------------------------------------------
*/
Route::get('/membership', [MembershipController::class, 'index'])
    ->middleware('membership.enabled')
    ->name('membership.index');

Route::prefix('membership')->name('membership.')->middleware('membership.enabled')->group(function () {
    Route::get('/benefits', [MembershipController::class, 'benefits'])
        ->name('benefits');

    Route::get('/privilege-redemption', [MembershipController::class, 'privilegeRedemption'])
        ->name('privilege-redemption');

    Route::get('/verify-email/{member}/{hash}', [MembershipAuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verify.email');

    Route::middleware('guest:member')->group(function () {
        Route::get('/sign-in', [MembershipAuthController::class, 'showLoginForm'])
            ->name('login');

        Route::post('/sign-in', [MembershipAuthController::class, 'login'])
            ->name('login.submit');

        Route::get('/sign-in/{provider}', [MembershipAuthController::class, 'redirectToSocialProvider'])
            ->whereIn('provider', ['google'])
            ->name('social.redirect');

        Route::get('/sign-in/{provider}/callback', [MembershipAuthController::class, 'handleSocialProviderCallback'])
            ->whereIn('provider', ['google'])
            ->name('social.callback');

        Route::get('/forgot-password', [MembershipAuthController::class, 'showForgotPasswordForm'])
            ->name('password.request');

        Route::post('/forgot-password', [MembershipAuthController::class, 'sendResetLinkEmail'])
            ->name('password.email');

        Route::get('/reset-password/{token}', [MembershipAuthController::class, 'showResetPasswordForm'])
            ->name('password.reset');

        Route::post('/reset-password', [MembershipAuthController::class, 'resetPassword'])
            ->name('password.store');

        Route::get('/join', [MembershipAuthController::class, 'showRegisterForm'])
            ->name('register');

        Route::post('/join', [MembershipAuthController::class, 'register'])
            ->name('register.submit');
    });

    Route::middleware('auth:member')->group(function () {
        Route::get('/change-password', [MembershipAuthController::class, 'showChangePasswordForm'])
            ->name('password.change');

        Route::post('/change-password', [MembershipAuthController::class, 'updatePassword'])
            ->name('password.update');

        Route::get('/dashboard', [MembershipAuthController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/profile/edit', [MembershipProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/profile', [MembershipProfileController::class, 'update'])
            ->name('profile.update');

        Route::get('/rewards/redemptions/{redemption}/thank-you', [MemberRewardRedemptionController::class, 'thankYou'])
            ->name('rewards.thank-you');

        Route::post('/rewards/{reward}/redeem', [MemberRewardRedemptionController::class, 'store'])
            ->name('rewards.redeem');

        Route::post('/logout', [MembershipAuthController::class, 'logout'])
            ->name('logout');
    });
});

/*
|--------------------------------------------------------------------------
| Accommodations
|--------------------------------------------------------------------------
*/
Route::get('/the-royal-suites-and-jungle-villas', [AccommodationController::class, 'index'])
    ->name('accommodations.index');

Route::get('/jungle-villas', [AccommodationController::class, 'villas'])
    ->name('accommodations.villas');

Route::get('/the-royal-suites', [AccommodationController::class, 'suites'])
    ->name('accommodations.suites');

Route::get('/the-royal-suite/presidential-royal-suite', [AccommodationController::class, 'presidentialRoyalSuite'])
    ->name('accommodations.presidential-royal-suite.show');

Route::redirect('/the-royal-suites/presidential-royal-suite', '/the-royal-suite/presidential-royal-suite', 301);

Route::get('/{type}/{accommodation:slug}', [AccommodationController::class, 'show'])
    ->whereIn('type', ['jungle-villas', 'the-royal-suites'])
    ->name('accommodations.show')
    ->missing(fn() => redirect()->route(
        request()->route('type') === 'the-royal-suites'
            ? 'accommodations.suites'
            : 'accommodations.villas',
        [],
        301
    ));

/*
|--------------------------------------------------------------------------
| Offers
|--------------------------------------------------------------------------
*/
Route::get('/offers', [OfferController::class, 'index'])
    ->name('offers.index');

Route::get('/offer/{offer:slug}', [OfferController::class, 'show'])
    ->name('offers.show')
    ->missing(fn() => redirect()->route('offers.index', [], 301));

/*
|--------------------------------------------------------------------------
| Experiences
|--------------------------------------------------------------------------
*/
Route::get('/experiences', [ExperienceController::class, 'index'])
    ->name('experiences.index');

Route::get('/experiences/{categorySlug}', [ExperienceController::class, 'index'])
    ->where('categorySlug', '[A-Za-z0-9\-]+')
    ->name('experiences.category');

Route::get('/experience/{experience:slug}', [ExperienceController::class, 'show'])
    ->name('experiences.show')
    ->missing(fn() => redirect()->route('experiences.index', [], 301));

/*
|--------------------------------------------------------------------------
| Holy River
|--------------------------------------------------------------------------
*/
Route::get('/holy-river', [HolyRiverController::class, 'index'])
    ->name('holy-river.index');

Route::get('/holy-river/{experience:slug}', [HolyRiverController::class, 'show'])
    ->name('holy-river.show');

/*
|--------------------------------------------------------------------------
| Little Things
|--------------------------------------------------------------------------
*/
Route::get('/the-little-things', [LittleThingsController::class, 'index'])
    ->name('little-things.index');

/*
|--------------------------------------------------------------------------
| Honeymoon
|--------------------------------------------------------------------------
*/
Route::get('/honeymoon', [HoneymoonController::class, 'index'])
    ->name('honeymoon.index');

Route::get('/honeymoon/{slug}', [HoneymoonController::class, 'show'])
    ->name('honeymoon.show');

/*
|--------------------------------------------------------------------------
| Dining
|--------------------------------------------------------------------------
*/
Route::get('/dining', [DiningController::class, 'index'])
    ->name('dining.index');

/*
|--------------------------------------------------------------------------
| Spa
|--------------------------------------------------------------------------
*/
Route::get('/spa-wellness', [SpaController::class, 'index'])
    ->name('spa.index');

Route::get('/spa-wellness/{slug}', [SpaController::class, 'show'])
    ->name('spa.show');

/*
|--------------------------------------------------------------------------
| Wedding
|--------------------------------------------------------------------------
*/
Route::get('/weddings', [WeddingController::class, 'index'])
    ->name('wedding.index');

/*
|--------------------------------------------------------------------------
| Sustainability
|--------------------------------------------------------------------------
*/
Route::get('/sustainability', [SustainabilityController::class, 'index'])
    ->name('sustainability.index');

/*
|--------------------------------------------------------------------------
| About Us
|--------------------------------------------------------------------------
*/
Route::get('/about-us', [AboutUsController::class, 'index'])
    ->name('about-us.index');

/*
|--------------------------------------------------------------------------
| Blog & News
|--------------------------------------------------------------------------
*/
Route::get('/blog-news', [BlogController::class, 'index'])
    ->name('blog.index');

Route::get('/blog-news/page/{page}', [BlogController::class, 'index'])
    ->whereNumber('page')
    ->name('blog.page');

Route::get('/blog-news/preview/{blogNews:slug}', [BlogController::class, 'preview'])
    ->middleware('signed')
    ->name('blog.preview');

Route::get('/blog-news/{slug}', [BlogController::class, 'show'])
    ->name('blog.show');

/*
|--------------------------------------------------------------------------
| Awards
|--------------------------------------------------------------------------
*/
Route::get('/awards', [AwardController::class, 'index'])
    ->name('awards.index');

Route::get('/awards/page/{page}', [AwardController::class, 'index'])
    ->whereNumber('page')
    ->name('awards.page');

/*
|--------------------------------------------------------------------------
| Gallery
|--------------------------------------------------------------------------
*/
Route::get('/gallery', [GalleryController::class, 'index'])
    ->name('gallery.index');

/*
|--------------------------------------------------------------------------
| FAQ
|--------------------------------------------------------------------------
*/
Route::get('/faq', [FaqController::class, 'index'])
    ->name('faq.index');

/*
|--------------------------------------------------------------------------
| Contact Us
|--------------------------------------------------------------------------
*/
Route::get('/contact-us', [ContactController::class, 'index'])
    ->name('contact.index');

Route::post('/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('inquiries.store');

Route::get('/test-mail/{token}', MailTestController::class)
    ->name('test-mail');

Route::get('/member-email-preview/{token}/{template?}', MemberEmailPreviewController::class)
    ->name('member-email-preview.show');

/*
|--------------------------------------------------------------------------
| Temporary WebHotelier PULL Tests
|--------------------------------------------------------------------------
*/
Route::get('/test-webhotelier-pending/{token}', function (string $token, WebhotelierPullService $service) {
    abort_unless(
        hash_equals((string) config('services.webhotelier.sync_token'), $token),
        403
    );

    try {
        return response()->json([
            'success' => true,
            'step' => '01_list_pending_bookings',
            'endpoint' => '/reservation/new',
            'config' => $service->configStatus(),
            'response' => $service->listPendingBookings(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'step' => '01_list_pending_bookings',
            'endpoint' => '/reservation/new',
            'config' => $service->configStatus(),
            'message' => $e->getMessage(),
        ], 500);
    }
})->name('test.webhotelier.pending');

Route::get('/test-webhotelier-reservation/{token}/{reservationId}', function (
    string $token,
    string $reservationId,
    WebhotelierPullService $service
) {
    abort_unless(
        hash_equals((string) config('services.webhotelier.sync_token'), $token),
        403
    );

    try {
        return response()->json([
            'success' => true,
            'step' => '02_retrieve_booking',
            'endpoint' => '/reservation/' . $reservationId,
            'reservation_id' => $reservationId,
            'response' => $service->retrieveBooking($reservationId),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'step' => '02_retrieve_booking',
            'endpoint' => '/reservation/' . $reservationId,
            'reservation_id' => $reservationId,
            'message' => $e->getMessage(),
        ], 500);
    }
})->name('test.webhotelier.reservation');

Route::get('/test-webhotelier-mark-synced/{token}/{reservationId}', function (
    string $token,
    string $reservationId,
    WebhotelierPullService $service
) {
    abort_unless(
        hash_equals((string) config('services.webhotelier.sync_token'), $token),
        403
    );

    try {
        return response()->json([
            'success' => true,
            'step' => '04_mark_booking_as_synced',
            'endpoint' => '/reservation/sync/' . $reservationId,
            'reservation_id' => $reservationId,
            'response' => $service->markBookingAsSynced($reservationId),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'step' => '04_mark_booking_as_synced',
            'endpoint' => '/reservation/sync/' . $reservationId,
            'reservation_id' => $reservationId,
            'message' => $e->getMessage(),
        ], 500);
    }
})->name('test.webhotelier.mark-synced');

/*
|--------------------------------------------------------------------------
| Cron
|--------------------------------------------------------------------------
*/
Route::get('/cron/webhotelier/sync/{token}', WebhotelierSyncController::class)
    ->name('cron.webhotelier.sync');

Route::get('/cron/bookings/sync/{token}', BookingSyncController::class)
    ->name('cron.bookings.sync');

Route::get('/cron/members/lifecycle/{token}', MembershipLifecycleController::class)
    ->name('cron.members.lifecycle');

Route::get('/cron/members/checkout-notifications/{token}', MemberCheckoutNotificationController::class)
    ->name('cron.members.checkout-notifications');

Route::get('/cron/offers/publication/{token}', OfferPublicationController::class)
    ->name('cron.offers.publication');

Route::get('/cron/blog-news/publication/{token}', BlogNewsPublicationController::class)
    ->name('cron.blog-news.publication');

Route::get('/cron/members/test-welcome-email/{token}', TestWelcomeEmailController::class)
    ->name('cron.members.test-welcome-email');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('pages.show');
