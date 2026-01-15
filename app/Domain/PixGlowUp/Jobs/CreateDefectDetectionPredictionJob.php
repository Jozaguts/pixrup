<?php

namespace App\Domain\PixGlowUp\Jobs;

use App\Domain\PixGlowUp\Services\DefectDetectionClient;
use App\Models\GlowupJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateDefectDetectionPredictionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    /**
     * 30 seconds between retries
     */
    public function backoff(): array
    {
        return [30, 30, 30, 30];
    }

    public function __construct(
        protected GlowupJob $glowUpJob,
    ) {}

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function handle(DefectDetectionClient $client): void
    {
        $lockKey = $this->getCacheKey();

        $lock = Cache::lock($lockKey, 90 * 60); // 90 minutes

        // If another worker is already processing this image → exit safely
        if (! $lock->get()) {
            Log::info('[PixGlowUp] Defect detection skipped (lock active)', $this->predictionKeys());

            return;
        }

        try {
            $predictionId = $client->createPrediction($this->glowUpJob->before_url);
            $pollingDelaySeconds = now()->addSeconds(90);

            Log::info('[PixGlowUp] Prediction created', $this->predictionKeys());

            PollDefectDetectionPredictionJob::dispatch(
                $predictionId,
                $this->glowUpJob,
            )
                ->delay($pollingDelaySeconds);

        } catch (Throwable $exception) {
            Log::error('[PixGlowUp] Failed creating defect prediction', [
                ...$this->predictionKeys(),
                'attempt'     => $this->attempts(),
                'error'       => $exception->getMessage(),
            ]);

            // Release lock so retries can happen,
            optional($lock)?->release();

            throw $exception;
        }
    }

    /**
     * Clear lock if job permanently fails
     */
    public function failed(Throwable $exception): void
    {
        //release lock after all retries have been exhausted.
        Cache::lock($this->getCacheKey())->forceRelease();

        Log::critical('[PixGlowUp] Defect detection permanently failed', [
            ...$this->predictionKeys(),
            'error'       => $exception->getMessage(),
        ]);
    }

    /**
     * Deterministic cache lock key
     */
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
            'glow_up_id'   => $this->glowUpJob->id,
            'image_url'   => $this->glowUpJob->before_url,
        ];
    }
}
