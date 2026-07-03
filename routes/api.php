<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingSyncFeedController;
use App\Http\Controllers\Api\WebhotelierReservationController;

Route::match(['get', 'post'], '/webhotelier/reservation/{secret}', [WebhotelierReservationController::class, 'store'])
    ->name('api.webhotelier.reservation');

Route::get('/bookings/sync', BookingSyncFeedController::class)
    ->name('api.bookings.sync');
