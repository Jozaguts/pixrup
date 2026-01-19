<?php

use App\Interface\Properties\Http\Controllers\BillingController;

Route::get('billing/account', [BillingController::class, 'account'])->name('billing.account');
Route::get('billing/order-history', [BillingController::class, 'orderHistory'])->name('billing.order-history');
Route::post('billing/payment-method', [BillingController::class, 'store'])->name('billing.payment-method.store');
Route::post('billing/payment-method/default', [BillingController::class, 'updateDefault'])->name('billing.payment-method.default');
Route::delete('billing/payment-method', [BillingController::class, 'destroy'])->name('billing.payment-method.destroy');
Route::post('billing/subscription', [BillingController::class, 'subscribe'])->name('billing.subscription.store');
Route::post('billing/subscription/swap', [BillingController::class, 'swap'])->name('billing.subscription.swap');
Route::post('billing/subscription/cancel', [BillingController::class, 'cancel'])->name('billing.subscription.cancel');
