<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\HolyRiverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| Accommodations
|--------------------------------------------------------------------------
*/
Route::get('/the-royal-suites-jungle-villas', [AccommodationController::class, 'index'])
    ->name('accommodations.index');

Route::get('/{type}/{accommodation:slug}', [AccommodationController::class, 'show'])
    ->whereIn('type', ['jungle-villa', 'the-royal-suite'])
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
