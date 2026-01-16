<?php

use App\Http\Controllers\GlowUp\GlowUpJobController;
use App\Interface\Properties\Http\Controllers\PropertyController;
use App\Interface\Appraisal\Http\Controllers\PropertyWorthController;
use App\Interface\Properties\Http\Controllers\SpyHuntController;

Route::prefix('properties')->group(static function() {
    Route::get('', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('new', [PropertyController::class, 'create'])->name('properties.new');
    Route::post('', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('{property}/overview', [PropertyController::class, 'overview'])->name('properties.overview');
    Route::post('{property}/worth/fetch', [PropertyWorthController::class, 'fetch'])->name('properties.worth.fetch');
    Route::get('{property}/glowup/jobs', [GlowUpJobController::class, 'index'])->name('properties.glowup.jobs.index');
    Route::post('{property}/glowup/jobs', [GlowUpJobController::class, 'store'])->name('properties.glowup.jobs.store');
    Route::get('{property}/glowup/jobs/{glowupJob}', [GlowUpJobController::class, 'show'])->name('properties.glowup.jobs.show');
    Route::get('{property}/fetch', [SpyHuntController::class, 'fetch'])->name('properties.spyhunt.fetch');
    Route::get('{property}/mls-refresh', [SpyHuntController::class, 'mls-refresh'])->name('properties.spyhunt.msl-refresh');
});
