<?php

use App\Http\Controllers\Api\PropertyController as ApiPropertyController;
use App\Http\Controllers\Api\PropertyWorthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(function (): void {
        Route::post('/properties', [ApiPropertyController::class, 'store'])->name('api.properties.store');
        Route::get('/properties/{property}/worth', [PropertyWorthController::class, 'show'])->name('api.properties.worth.show');
        Route::post('/properties/{property}/worth/fetch', [PropertyWorthController::class, 'fetch'])->name('api.properties.worth.fetch');
        Route::post('/properties/{property}/worth/report', [PropertyWorthController::class, 'attachToReport'])->name('api.properties.worth.report');
    });
