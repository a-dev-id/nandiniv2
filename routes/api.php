<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhotelierReservationController;

Route::match(['get', 'post'], '/webhotelier/reservation/{secret}', [WebhotelierReservationController::class, 'store'])
    ->name('api.webhotelier.reservation');
