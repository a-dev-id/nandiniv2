<?php

use App\Http\Controllers\DiningLandingController;
use App\Http\Controllers\DiningExperienceController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SignatureDishController;
use Illuminate\Support\Facades\Route;

Route::domain(config('domains.dining'))->group(function (): void {
    Route::get('/', DiningLandingController::class)->name('dining-landing.index');
    Route::get('/experiences/{experience}', DiningExperienceController::class)
        ->name('dining-landing.experiences.show');
    Route::get('/signature-dishes/{signatureDish}', SignatureDishController::class)
        ->name('dining-landing.signature-dishes.show');
    Route::post('/inquiries', [InquiryController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('dining-landing.inquiries.store');
});
