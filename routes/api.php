<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailRelayController;
use App\Http\Controllers\Api\WebhotelierReservationController;
use App\Http\Controllers\Voucher\FlywireNotificationController;

Route::match(['get', 'post'], '/webhotelier/reservation/{secret}', [WebhotelierReservationController::class, 'store'])
    ->name('api.webhotelier.reservation');

Route::post('/flywire/notifications', FlywireNotificationController::class)
    ->middleware('voucher.enabled')
    ->name('api.flywire.notifications');

Route::post('/email-relay/send', EmailRelayController::class)
    ->middleware('throttle:30,1')
    ->name('api.email-relay.send');
