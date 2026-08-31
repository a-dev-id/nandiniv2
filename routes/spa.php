<?php

use App\Http\Controllers\SpaSite\HomeController;
use App\Http\Controllers\SpaSite\PageController;
use Illuminate\Support\Facades\Route;

Route::domain(config('domains.spa'))
    ->middleware('spa.enabled')
    ->name('spa-site.')
    ->group(function (): void {
        Route::get('/', HomeController::class)
            ->name('home');

        Route::get('/{slug}', [PageController::class, 'show'])
            ->where('slug', '[A-Za-z0-9\-]+')
            ->name('pages.show');
    });
