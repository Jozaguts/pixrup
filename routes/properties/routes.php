<?php

use App\Http\Controllers\GlowUp\GlowUpJobController;
use App\Interface\Properties\Http\Controllers\PropertyController;
use App\Interface\Appraisal\Http\Controllers\PropertyWorthController;
use App\Interface\Properties\Http\Controllers\SpyHuntController;

Route::prefix('properties')
    ->name('properties.')
    ->group(static function() {
    Route::get('', [PropertyController::class, 'index'])->name('index');
    Route::get('new', [PropertyController::class, 'create'])->name('new');
    Route::post('', [PropertyController::class, 'store'])->name('store');
    Route::get('{property}', [PropertyController::class, 'show'])->name('show');
    Route::get('{property}/overview', [PropertyController::class, 'overview'])->name('overview');
    Route::post('{property}/worth/fetch', [PropertyWorthController::class, 'fetch'])->name('worth.fetch');
    Route::get('{property}/glowup/jobs', [GlowUpJobController::class, 'index'])->name('glowup.jobs.index');
    Route::post('{property}/glowup/jobs', [GlowUpJobController::class, 'store'])->name('glowup.jobs.store');
    Route::get('{property}/glowup/jobs/{glowupJob}', [GlowUpJobController::class, 'show'])->name('glowup.jobs.show');
    Route::get('{property}/fetch', [SpyHuntController::class, 'fetch'])->name('spyhunt.fetch');
    Route::get('{property}/mls-refresh', [SpyHuntController::class, 'mls-refresh'])->name('spyhunt.msl-refresh');
});
