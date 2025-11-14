<?php

use App\Http\Controllers\Api\UsageSummaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlowUp\GlowUpJobController;
use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\Properties\PropertyWorthController as LegacyPropertyWorthController;
use App\Models\Property;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Interface\Appraisal\Http\Controllers\PropertyWorthController as AppraisalPropertyWorthController;
use App\Interface\Auth\Http\Controllers\AuthController;
use App\Interface\Auth\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/properties/new', [PropertyController::class, 'create'])->name('properties.new');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::post('/properties/{property}/worth/fetch', [AppraisalPropertyWorthController::class, 'fetch'])->name('properties.worth.fetch');
    Route::post('/properties/{property}/worth/report', [LegacyPropertyWorthController::class, 'report'])->name('properties.worth.report');
    Route::get('/glowup/jobs', [GlowUpJobController::class, 'history'])->name('glowup.jobs.index');
    Route::get('/properties/{property}/glowup/jobs', [GlowUpJobController::class, 'index'])->name('properties.glowup.jobs.index');
    Route::post('/properties/{property}/glowup/jobs', [GlowUpJobController::class, 'store'])->name('properties.glowup.jobs.store');
    Route::get('/properties/{property}/glowup/jobs/{glowupJob}', [GlowUpJobController::class, 'show'])->name('properties.glowup.jobs.show');
    Route::post('/glowup/jobs/{glowupJob}/attach', [GlowUpJobController::class, 'attach'])->name('glowup.jobs.attach');
    Route::get('/v1/usage', UsageSummaryController::class)->name('usage.summary');

});

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.store');

Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
require __DIR__.'/settings.php';
