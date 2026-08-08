<?php

use App\Http\Controllers\AffiliateShortLinkController;
use Illuminate\Support\Facades\Route;

Route::domain(config('domains.short_link'))
    ->middleware(['web', 'affiliate.enabled'])
    ->name('affiliate.short-link.')
    ->get('/{affiliate_code}', AffiliateShortLinkController::class)
    ->where('affiliate_code', '[A-Za-z0-9]+')
    ->name('redirect');
