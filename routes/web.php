<?php

use App\Http\Controllers\Api\UsageSummaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlowUp\GlowUpJobController;
use App\Http\Controllers\Billing\StripeWebhookController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia::render('welcome/index', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('test/{property}', [
    DashboardController::class,
    'generateReport'
])
    ->name('report.test');

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/glowup/jobs', [GlowUpJobController::class, 'history'])->name('glowup.jobs.index');

    Route::post('/glowup/jobs/{glowupJob}/attach', [GlowUpJobController::class, 'attach'])->name('glowup.jobs.attach');
    Route::post('/glowup/jobs/{glowupJob}/detach', [GlowUpJobController::class, 'detach'])->name('glowup.jobs.detach');
    Route::get('/v1/usage', UsageSummaryController::class)->name('usage.summary');

    Route::get('plan/upgrade', static fn() => Inertia::render('plan/upgrade/Index',[]))
        ->name('plan.upgrade');
    require __DIR__.'/billing/routes.php';
    require __DIR__.'/reports/routes.php';
    require __DIR__.'/settings.php';
    require __DIR__.'/properties/routes.php';
});

require __DIR__.'/guest/routes.php';

