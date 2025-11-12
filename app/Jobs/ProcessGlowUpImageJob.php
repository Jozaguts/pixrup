<?php

namespace App\Jobs;

use App\Domain\GlowUp\Contracts\GlowUpImageProvider;
use App\Events\GlowUpJobUpdated;
use App\Models\GlowupJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ProcessGlowUpImageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $jobId,
    ) {
    }

    public function handle(
        GlowUpImageProvider $imageProvider,
    ): void {
        $job = GlowupJob::query()->find($this->jobId);

        if (! $job) {
            return;
        }

        $job->forceFill([
            'status' => GlowupJob::STATUS_PROCESSING,
            'error_message' => null,
        ])->save();

        GlowUpJobUpdated::dispatch($job->fresh());

        $disk = data_get($job->meta, 'disk', config('glowup.disk', 'public'));
        $beforePath = data_get($job->meta, 'before_path');

        if (! $beforePath) {
            throw new RuntimeException('Missing source path for GlowUp job.');
        }

        $afterPath = $imageProvider->generate($job, $beforePath, $disk);
        $afterUrl = Storage::disk($disk)->url($afterPath);

        $meta = $job->meta ?? [];
        data_set($meta, 'after_path', $afterPath);

        $job->forceFill([
            'after_url' => $afterUrl,
            'status' => GlowupJob::STATUS_DONE,
            'meta' => $meta,
        ])->save();

        GlowUpJobUpdated::dispatch($job->fresh());
    }

    public function failed(Throwable $exception): void
    {
        $job = GlowupJob::query()->find($this->jobId);

        if (! $job) {
            return;
        }

        $job->forceFill([
            'status' => GlowupJob::STATUS_ERROR,
            'error_message' => $exception->getMessage(),
        ])->save();

        GlowUpJobUpdated::dispatch($job->fresh());
    }
}
