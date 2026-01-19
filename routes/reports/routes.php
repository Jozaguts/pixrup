<?php


use App\Http\Controllers\Reports\PdfReportsController;

Route::prefix('reports')
    ->name('reports')
    ->group( static function() {
        Route::get('/',[
            PdfReportsController::class,
            'index'
        ])->name('pdf.index');

        Route::get('/new',[
            PdfReportsController::class,
            'new'
        ])->name('pdf.new');

        Route::get('/logos',[
            PdfReportsController::class,
            'getLogos'
        ])->name('pdf.logos');

        Route::post('/logos/create',[
            PdfReportsController::class,
            'storeLogo'
        ])->name('pdf.logos.new');
    });