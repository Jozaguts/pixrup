<?php

namespace App\Infrastructure\GlowUp\Providers;

use App\Domain\GlowUp\Contracts\GlowUpImageProvider;
use App\Models\GlowupJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FakeAiImageService implements GlowUpImageProvider
{
    public function generate(GlowupJob $job, string $sourcePath, string $disk): string
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($sourcePath)) {
            throw new RuntimeException("Source image not found: {$sourcePath}");
        }

        $prompt = data_get($job->meta, 'prompt');
        if (is_string($prompt) && $prompt !== '') {
            Log::info('fake-glowup.prompt', [
                'job_id' => $job->getKey(),
                'property_id' => $job->property_id,
                'prompt' => $prompt,
            ]);
        }

        $contents = $storage->get($sourcePath);
        $processed = $this->applyLook($contents);

        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'jpg';
        $target = sprintf(
            'glowup/%d/after/%s-%s.%s',
            $job->property_id,
            $job->getKey(),
            Str::random(8),
            $extension,
        );

        $storage->put($target, $processed, ['visibility' => 'public']);

        return $target;
    }

    private function applyLook(string $contents): string
    {
        if (! extension_loaded('gd')) {
            return $contents;
        }

        $image = @imagecreatefromstring($contents);

        if ($image === false) {
            return $contents;
        }

        imagefilter($image, IMG_FILTER_COLORIZE, 14, 2, 42, 25);
        imagefilter($image, IMG_FILTER_BRIGHTNESS, 8);
        imagefilter($image, IMG_FILTER_CONTRAST, -6);
        imagefilter($image, IMG_FILTER_SMOOTH, 8);

        ob_start();
        imagejpeg($image, null, 92);
        imagedestroy($image);

        $buffer = ob_get_clean();

        return $buffer !== false ? $buffer : $contents;
    }
}
