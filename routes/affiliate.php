<?php

use App\Http\Controllers\AffiliateAuthController;
use App\Http\Controllers\AffiliateDashboardController;
use App\Http\Controllers\AffiliateDashboardWelcomeController;
use App\Http\Controllers\AffiliateEmailVerificationController;
use App\Http\Controllers\AffiliateLandingController;
use App\Http\Controllers\AffiliateMarketingAssetController;
use App\Http\Controllers\AffiliatePasswordSetupController;
use App\Http\Controllers\AffiliatePaymentProfileController;
use App\Http\Controllers\AffiliateProfileController;
use App\Http\Controllers\AffiliateRegistrationController;
use App\Http\Controllers\AffiliateReportController;
use App\Http\Middleware\SeparateMemberAndAffiliateSessions;
use Illuminate\Support\Facades\Route;

Route::domain(config('domains.affiliate'))
    ->name('affiliate.')
    ->middleware(['web', 'affiliate.enabled', SeparateMemberAndAffiliateSessions::class])
    ->group(function (): void {
        Route::get('/', AffiliateLandingController::class)->name('landing');
        Route::get('/verify-email/{affiliate}/{hash}', [AffiliateEmailVerificationController::class, 'verify'])
            ->middleware('throttle:12,1')
            ->name('verification.verify');

        Route::middleware(['affiliate.registration.guest', 'affiliate.registration.enabled'])->group(function (): void {
            Route::get('/register', [AffiliateRegistrationController::class, 'create'])->name('register');
            Route::post('/register', [AffiliateRegistrationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('register.submit');
        });

        Route::middleware('affiliate.registration.guest')->group(function (): void {
            Route::get('/login', [AffiliateAuthController::class, 'create'])->name('login');
            Route::post('/login', [AffiliateAuthController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('login.submit');
            Route::get('/login/{provider}', [AffiliateAuthController::class, 'redirectToSocialProvider'])
                ->whereIn('provider', ['google'])
                ->name('social.redirect');
            Route::get('/login/{provider}/callback', [AffiliateAuthController::class, 'handleSocialProviderCallback'])
                ->whereIn('provider', ['google'])
                ->name('social.callback');
            Route::get('/set-password/{token}', [AffiliatePasswordSetupController::class, 'create'])->name('password.setup');
            Route::post('/set-password', [AffiliatePasswordSetupController::class, 'store'])->name('password.update');
        });

        Route::middleware('affiliate.auth')->group(function (): void {
            Route::post('/email/verification-notification', [AffiliateEmailVerificationController::class, 'resend'])
                ->middleware('throttle:3,1')
                ->name('verification.send');
            Route::get('/dashboard', AffiliateDashboardController::class)->name('dashboard');
            Route::post('/dashboard/welcome/dismiss', AffiliateDashboardWelcomeController::class)
                ->middleware('throttle:10,1')
                ->name('dashboard.welcome.dismiss');
            Route::get('/profile', AffiliateProfileController::class)->name('profile');
            Route::get('/payment-details', [AffiliatePaymentProfileController::class, 'edit'])->name('payment-details.edit');
            Route::put('/payment-details', [AffiliatePaymentProfileController::class, 'update'])->name('payment-details.update');
            Route::get('/marketing-materials', [AffiliateMarketingAssetController::class, 'index'])->name('marketing-materials.index');
            Route::get('/marketing-materials/{asset}/download', [AffiliateMarketingAssetController::class, 'download'])->name('marketing-materials.download');
            Route::get('/marketing-materials/{asset}/preview', [AffiliateMarketingAssetController::class, 'preview'])->name('marketing-materials.preview');
            Route::get('/reports', [AffiliateReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export/{type}', [AffiliateReportController::class, 'export'])->name('reports.export');
            Route::post('/logout', [AffiliateAuthController::class, 'destroy'])->name('logout');
        });
    });
