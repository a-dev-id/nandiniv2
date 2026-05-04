<?php

use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\HolyRiverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

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
