<?php

namespace App\Http\Resources\GlowUp;

use App\Models\GlowupJob;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GlowupJob
 */
class GlowUpJobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var GlowupJob $job */
        $job = $this->resource;

        return [
            'id' => $job->getKey(),
            'property_id' => $job->property_id,
            'user_id' => $job->user_id,
            'room_type' => $job->room_type,
            'style' => $job->style,
            'before_url' => $job->before_url,
            'after_url' => $job->after_url,
            'status' => $job->status,
            'error_message' => $job->error_message,
            'progress' => $this->progressForStatus($job->status),
            'is_terminal' => $job->isTerminal(),
            'created_at' => optional($job->created_at)->toIso8601String(),
            'updated_at' => optional($job->updated_at)->toIso8601String(),
            'usage_recorded_at' => optional($job->usage_recorded_at)->toIso8601String(),
            'meta' => [
                'prompt' => data_get($job->meta, 'prompt'),
            ],
        ];
    }

    private function progressForStatus(string $status): int
    {
        return match ($status) {
            GlowupJob::STATUS_PENDING => 10,
            GlowupJob::STATUS_PROCESSING => 55,
            GlowupJob::STATUS_DONE => 100,
            default => 0,
        };
    }
}
