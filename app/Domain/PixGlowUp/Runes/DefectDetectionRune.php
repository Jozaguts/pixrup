<?php

namespace App\Domain\PixGlowUp\Runes;

use App\Domain\PixGlowUp\Concerns\HandlesRuneDefaults;
use App\Models\GlowupJob;

class DefectDetectionRune
{
    use HandlesRuneDefaults;

    public function __construct(
        protected GlowupJob $job,
        protected array $output,
        protected string $predictionId
    ) {}

    /**
     * Dynamic rune identity
     */
    protected function runeName(): string
    {
        return 'defect_detection_' . $this->predictionId;
    }

    protected function runeProvider(): string
    {
        return 'pix_glow_up';
    }

    protected function propertyId(): int
    {
        return $this->job->property_id;
    }

    /**
     * Public PixVision payload
     */
    public function runePayload(): array
    {
        return [
            'rune_value' => [
                'glow_up_job_id' => $this->job->id,
                'image_url'      => $this->job->before_url,
                'defects'        => $this->output,
            ],
            'confidence' => $this->confidence(),
        ];
    }

    /**
     * Confidence is derived from the strongest visible signal
     */
    protected function confidence(): float
    {
        $defects = $this->output['defects'] ?? $this->output ?? [];

        return round(
            collect($defects)
                ->filter(fn ($d) => ($d['detected'] ?? false) === true)
                ->max('confidence') ?? 0.0,
            2
        );
    }
}
