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

Route::get('/test/{property}', static function(\App\Models\Property $property) {
    $useCase = new  \App\Infrastructure\PixVision\UseCases\GeneratePixVisionReport($property);
    $data =   $useCase->execute();
    $html = view('report.index', $data)->render();

    $dompdf= \Barryvdh\DomPDF\Facade\Pdf::
        loadHTML($html)->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])
        ->setPaper('A4');
    $dompdf->render();
    $canvas = $dompdf->getCanvas();
    $w = $canvas->get_width();
    $h = $canvas->get_height();
    $img = $data['logo'] ;// watermark image
    $canvas->page_script(function($pageNumber, $pageCount, $canvas, $fontMetrics) use ($img, $w, $h) {
        $canvas->save(); // save current state
        $canvas->set_opacity(0.1);
        $canvas->translate($w/2, $h/2);
        $canvas->image($img, -($w/4), -($h/6), $w/2, $h/3);
        $canvas->restore(); // restore state
    });




    return $dompdf->stream('document.pdf');
});

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

