<?php

use App\Http\Controllers\Api\EmailRelayController;
use App\Http\Controllers\Api\MembershipEmailLookupController;
use App\Http\Controllers\Api\WebhotelierReservationController;
use App\Http\Controllers\Voucher\FlywireNotificationController;
use App\Http\Middleware\AuthenticateGuestLetterMembershipApi;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/webhotelier/reservation/{secret}', [WebhotelierReservationController::class, 'store'])
    ->name('api.webhotelier.reservation');

Route::post('/flywire/notifications', FlywireNotificationController::class)
    ->middleware('voucher.enabled')
    ->name('api.flywire.notifications');

Route::post('/email-relay/send', EmailRelayController::class)
    ->middleware('throttle:30,1')
    ->name('api.email-relay.send');

Route::post('/membership/check-email', MembershipEmailLookupController::class)
    ->middleware([AuthenticateGuestLetterMembershipApi::class, 'throttle:60,1'])
    ->name('api.membership.check-email');
