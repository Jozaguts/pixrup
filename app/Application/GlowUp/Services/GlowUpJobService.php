<?php

namespace App\Application\GlowUp\Services;

use App\Application\Shared\Services\CheckFeatureLimit;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Events\GlowUpJobUpdated;
use App\Jobs\ProcessGlowUpImageJob;
use App\Models\GlowupJob;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GlowUpJobService
{
    public function __construct(
        private readonly CheckFeatureLimit $featureLimit,
    ) {
    }

    /**
     * @throws FeatureLimitExceededException
     */
    public function create(
        Property $property,
        User $user,
        UploadedFile $image,
        array $payload,
    ): GlowupJob {
        $feature = config('glowup.feature_identifier');

        $this->featureLimit->assertUsageAvailable($user, $feature);

        $disk = $this->disk();
        try {
            $beforePath = $image->storePublicly(
                sprintf('glowup/%d/before', $property->getKey()),
                $disk,
            );

            if (! $beforePath) {
                logger('GlowUp upload failed', ['disk' => $disk]);
                throw new \RuntimeException('No se pudo subir la imagen, revisa los logs.');
            }
        }catch (\Exception $e){
            logger('catch', ['message' => $e->getMessage()]);
            throw new \RuntimeException('catch error',);
        }

        $beforeUrl = Storage::disk($disk)->url($beforePath);

        $job = GlowupJob::query()->create([
            'property_id' => $property->getKey(),
            'user_id' => $user->getKey(),
            'room_type' => $payload['room_type'],
            'style' => $payload['style'],
            'before_url' => $beforeUrl,
            'status' => GlowupJob::STATUS_PENDING,
            'meta' => [
                'disk' => $disk,
                'before_path' => $beforePath,
            ],
        ]);

        GlowUpJobUpdated::dispatch($job);

        ProcessGlowUpImageJob::dispatch($job->getKey());

        return $job;
    }

    public function markUsageIfNeeded(GlowupJob $job): void
    {
        if ($job->usage_recorded_at !== null) {
            return;
        }

        $user = $job->user;

        if ($user === null) {
            return;
        }

        $feature = config('glowup.feature_identifier');

        $this->featureLimit->recordUsage($user, $feature);

        $job->forceFill(['usage_recorded_at' => now()])->save();
    }

    public function attachResult(GlowupJob $job, string $action, ?string $notes): void
    {
        $property = $job->property;

        if ($property === null) {
            return;
        }

        $meta = $property->metadata ?? [];
        $attachments = data_get($meta, 'glowup.attachments', []);

        $attachments[] = [
            'job_id' => $job->getKey(),
            'after_url' => $job->after_url,
            'room_type' => $job->room_type,
            'style' => $job->style,
            'action' => $action,
            'notes' => $notes,
            'attached_at' => now()->toIso8601String(),
        ];

        data_set($meta, 'glowup.attachments', $attachments);

        $property->metadata = $meta;
        $property->save();
    }

    private function disk(): string
    {
        return config('glowup.disk', config('filesystems.default', 'public'));
    }
}
