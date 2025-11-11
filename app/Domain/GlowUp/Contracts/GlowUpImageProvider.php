<?php

namespace App\Domain\GlowUp\Contracts;

use App\Models\GlowupJob;

interface GlowUpImageProvider
{
    /**
     * Generate the "after" asset for the provided job.
     *
     * @return string Path (relative to disk root) where the rendered asset was stored.
     */
    public function generate(GlowupJob $job, string $sourcePath, string $disk): string;
}
