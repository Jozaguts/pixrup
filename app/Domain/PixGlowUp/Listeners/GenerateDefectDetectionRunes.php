<?php

namespace App\Domain\PixGlowUp\Listeners;

use App\Domain\PixGlowUp\Events\GlowUpGenerated;
use App\Domain\PixGlowUp\Jobs\CreateDefectDetectionPredictionJob;

class GenerateDefectDetectionRunes
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(GlowUpGenerated $event): void
    {
        CreateDefectDetectionPredictionJob::dispatch($event->glowUpJob);
    }
}
