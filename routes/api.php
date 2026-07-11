<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhotelierReservationController;
use App\Http\Controllers\Voucher\FlywireNotificationController;

Route::match(['get', 'post'], '/webhotelier/reservation/{secret}', [WebhotelierReservationController::class, 'store'])
    ->name('api.webhotelier.reservation');

Route::post('/flywire/notifications', FlywireNotificationController::class)
    ->name('api.flywire.notifications');
