<?php

use App\Http\Controllers\HolyRiverController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/offers', [OfferController::class, 'index'])
    ->name('offers.index');

Route::get('/offer/{offer:slug}', [OfferController::class, 'show'])
    ->name('offers.show');

Route::get('/holy-river', [HolyRiverController::class, 'index'])
    ->name('holy-river.index');
