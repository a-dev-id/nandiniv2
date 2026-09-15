<?php

use App\Http\Controllers\SpaLandingController;
use App\Http\Controllers\SpaLandingPageController;
use Illuminate\Support\Facades\Route;

Route::domain(config('domains.spa'))
    ->middleware('spa.enabled')
    ->name('spa-landing.')
    ->group(function (): void {
        Route::get('/', SpaLandingController::class)
            ->name('index');

        Route::get('/{slug}', [SpaLandingPageController::class, 'show'])
            ->where('slug', '[A-Za-z0-9\-]+')
            ->name('pages.show');
    });
