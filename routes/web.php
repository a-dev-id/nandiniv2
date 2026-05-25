<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\Cron\WebhotelierSyncController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\HolyRiverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipAuthController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\OfferController;
use App\Services\WebhotelierPullService;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| Auth Redirect Fallback
|--------------------------------------------------------------------------
| Laravel's auth middleware expects a route named "login".
| We redirect it to the member sign-in page.
*/
Route::get('/login', function () {
    return redirect()->route('membership.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Membership
|--------------------------------------------------------------------------
*/
Route::get('/membership', [MembershipController::class, 'index'])
    ->name('membership.index');

Route::prefix('membership')->name('membership.')->group(function () {
    Route::get('/verify-email/{member}/{hash}', [MembershipAuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verify.email');

    Route::middleware('guest:member')->group(function () {
        Route::get('/sign-in', [MembershipAuthController::class, 'showLoginForm'])
            ->name('login');

        Route::post('/sign-in', [MembershipAuthController::class, 'login'])
            ->name('login.submit');

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
    ->name('accommodations.show');

/*
|--------------------------------------------------------------------------
| Offers
|--------------------------------------------------------------------------
*/
Route::get('/offers', [OfferController::class, 'index'])
    ->name('offers.index');

Route::get('/offer/{offer:slug}', [OfferController::class, 'show'])
    ->name('offers.show');

/*
|--------------------------------------------------------------------------
| Experiences
|--------------------------------------------------------------------------
*/
Route::get('/experiences', [ExperienceController::class, 'index'])
    ->name('experiences.index');

Route::get('/experience/{experience:slug}', [ExperienceController::class, 'show'])
    ->name('experiences.show');

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
| Temporary WebHotelier PULL Tests
|--------------------------------------------------------------------------
| Remove these routes after the PULL flow is confirmed.
*/

/*
| Step 1:
| Test /reservation/new
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

/*
| Step 2:
| Test /reservation/{res_id}
| This only retrieves booking detail.
| It does NOT mark the booking as synced.
*/
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

/*
| Step 4:
| Test /reservation/sync/{res_id}
| WARNING:
| This removes the reservation from WebHotelier pending sync queue.
| Use this only after the reservation is already saved in local database.
*/
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
