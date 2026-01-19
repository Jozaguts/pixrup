<?php

namespace App\Domain\PixGlowUp\Jobs;

use App\Domain\PixGlowUp\Runes\DefectDetectionRune;
use App\Domain\PixGlowUp\Services\DefectDetectionClient;
use App\Models\GlowupJob;
use App\Models\PixVisionPropertyRune;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JsonException;
use Throwable;

class PollDefectDetectionPredictionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Max retries */
    public int $tries = 15;

    public function __construct(
        public string $predictionId,
        public GlowupJob $glowUpJob
    ) {}

    /** 30 seconds between retries */
    public function backoff(): int
    {
        return 30;
    }

    /**
     * @throws Throwable
     * @throws JsonException
     * @throws ConnectionException
     */
    public function handle(DefectDetectionClient $client): void
    {
        $cacheKey = $this->getCacheKey();

        try {
            $prediction = $client->fetchPredictionResult($this->predictionId);
            $status = $prediction['status'] ?? null;

            if (in_array($status, ['starting', 'processing'], true)) {
                Log::info('[PixGlowUp] Prediction still processing', [
                    ...$this->predictionKeys(),
                    'status'  => $status,
                    'attempt' => $this->attempts(),
                ]);

                $this->release($this->backoff());
                return;
            }

            if ($status === 'failed') {
                throw new \RuntimeException(
                    $prediction['error'] ?? 'Prediction failed'
                );
            }

            if ($status !== 'succeeded') {
                throw new \RuntimeException(
                    'Unexpected prediction status: '.$status
                );
            }

            $output = $prediction['output'] ?? null;

            if (! is_array($output)) {
                throw new \RuntimeException('Prediction output missing or invalid');
            }

            Log::info('[PixGlowUp] Prediction succeeded', [
                ...$this->predictionKeys(),
                'prediction_id' => $this->predictionId,
            ]);

            /** -----------------------------
             *  Generate and persist rune
             *  -----------------------------
             */
            $rune = new DefectDetectionRune(
                job: $this->glowUpJob,
                output: $output,
                predictionId: $this->predictionId,
            );

            $payload = $rune->toArray();

            PixvisionPropertyRune::updateOrCreate(
                [
                    'property_id' => $payload['property_id'],
                    'rune_key'    => $payload['rune_key'],
                    'provider'    => $payload['provider'],
                    'version'     => $payload['version'],
                ],
                [
                    'rune_value'  => $payload['rune_value'],
                    'confidence'  => $payload['confidence'],
                    'computed_at' => $payload['computed_at'],
                ]
            );

            //Clear lock ONLY after success
            Cache::lock($cacheKey)->forceRelease();

        } catch (Throwable $exception) {
            Log::error('[PixGlowUp] Polling prediction failed', [
                ...$this->predictionKeys(),
                'attempt' => $this->attempts(),
                'error'   => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * Called when retries are exhausted
     */
    public function failed(Throwable $exception): void
    {
        Cache::lock($this->getCacheKey())->forceRelease();

        Log::critical('[PixGlowUp] Prediction polling exhausted', [
            ...$this->predictionKeys(),
            'error' => $exception->getMessage(),
        ]);
    }

    protected function getCacheKey(): string
    {
        return 'pixglowup:defect_detection:' . sha1(
                implode('|', $this->predictionKeys())
            );
    }

    protected function predictionKeys(): array
    {
        return [
            'property_id' => $this->glowUpJob->property_id,
            'glow_up_id'  => $this->glowUpJob->id,
            'image_url'   => $this->glowUpJob->before_url,
        ];
    }
}
