<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\Cron\WebhotelierSyncController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\HolyRiverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipAuthController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\OfferController;
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
    Route::middleware('guest:member')->group(function () {
        Route::get('/sign-in', [MembershipAuthController::class, 'showLoginForm'])
            ->name('login');

        Route::post('/sign-in', [MembershipAuthController::class, 'login'])
            ->name('login.submit');

        Route::get('/join', [MembershipAuthController::class, 'showRegisterForm'])
            ->name('register');
    });

    Route::middleware('auth:member')->group(function () {
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

Route::get('/cron/webhotelier/sync/{token}', WebhotelierSyncController::class)
    ->name('cron.webhotelier.sync');
