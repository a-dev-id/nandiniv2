<?php

use App\Http\Controllers\Voucher\CartController;
use App\Http\Controllers\Voucher\CheckoutController;
use App\Http\Controllers\Voucher\OrderController;
use App\Http\Controllers\Voucher\PaymentController;
use App\Http\Controllers\Voucher\VoucherController;
use App\Http\Controllers\Voucher\VoucherVerificationController;
use Illuminate\Support\Facades\Route;

Route::domain(config('domains.voucher'))
    ->name('voucher.')
    ->middleware('web')
    ->group(function (): void {
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::get('/category/{voucherCategory:slug}', [VoucherController::class, 'category'])->name('category.show');
        Route::get('/voucher/{voucher:slug}', [VoucherController::class, 'show'])->name('show');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{voucher:slug}', [CartController::class, 'add'])->middleware('throttle:30,1')->name('cart.add');
        Route::put('/cart/{key}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{key}', [CartController::class, 'remove'])->name('cart.remove');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');

        Route::get('/payment/start/{order:order_number}', [PaymentController::class, 'start'])->name('payment.start');
        Route::get('/payment/return/{order?}', [PaymentController::class, 'return'])->name('payment.return');

        Route::get('/order/{orderNumber}', [OrderController::class, 'show'])->name('order.show');
        Route::get('/order/{orderNumber}/thank-you', [OrderController::class, 'thankYou'])->name('order.thank-you');
        Route::get('/verify/{token}', VoucherVerificationController::class)->middleware('throttle:20,1')->name('verify');
    });
