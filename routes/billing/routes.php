<?php

use App\Interface\Properties\Http\Controllers\BillingController;

Route::get('billing/account', [BillingController::class, 'account'])->name('billing.account');
Route::post('billing/payment-method', [BillingController::class, 'store'])->name('billing.payment-method.store');
