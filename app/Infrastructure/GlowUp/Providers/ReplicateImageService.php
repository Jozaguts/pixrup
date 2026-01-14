<?php

namespace App\Infrastructure\GlowUp\Providers;

use App\Domain\GlowUp\Contracts\GlowUpImageProvider;
use App\Models\GlowupJob;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ReplicateImageService implements GlowUpImageProvider
{
    private PendingRequest $http;
    private string $model;
    private ?string $modelOwner;
    private ?string $version;
    private string $promptTemplateSeedream;
    private string $promptTemplateSdxl;
    private string $negativePromptTemplateSdxl;
    private string $size;
    private string $aspectRatio;
    private int $maxImages;
    private float $strength;
    private float $guidanceScale;
    private int $steps;
    private int $width;
    private int $height;
    private string $waitPreference;

    public function __construct()
    {
        $token = config('services.replicate.token');
        if (! $token) {
            throw new RuntimeException('Missing Replicate API token.');
        }

        $baseUrl = rtrim(config('services.replicate.base_url', 'https://api.replicate.com/v1/'), '/') . '/';

        $this->http = Http::withToken($token)
            ->acceptJson()
            ->baseUrl($baseUrl)
            ->timeout((int) config('services.replicate.timeout', 120))
            ->connectTimeout(10)
            ->retry((int) config('services.replicate.retries', 2), 2000);

        $this->modelOwner = config('services.replicate.model_owner');
        $this->model = trim(config('services.replicate.model', 'seedream-4.5'));
        $this->version = config('services.replicate.version');
        $this->promptTemplateSeedream = (string) config('services.replicate.prompt_template_seedream');
        $this->promptTemplateSdxl = (string) config('services.replicate.prompt_template_sdxl');
        $this->negativePromptTemplateSdxl = (string) config('services.replicate.negative_prompt_template_sdxl', '');
        $this->size = config('services.replicate.size', '2K');
        $this->aspectRatio = config('services.replicate.aspect_ratio', '4:3');
        $this->maxImages = (int) config('services.replicate.max_images', 1);
        $this->strength = (float) config('services.replicate.strength', 0.85);
        $this->guidanceScale = (float) config('services.replicate.guidance_scale', 6);
        $this->steps = (int) config('services.replicate.steps', 35);
        $this->width = (int) config('services.replicate.width', 2048);
        $this->height = (int) config('services.replicate.height', 2048);
        $this->waitPreference = config('services.replicate.wait_preference', 'wait=60');
    }

    public function generate(GlowupJob $job, string $sourcePath, string $disk): string
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($sourcePath)) {
            throw new RuntimeException("Source image not found: {$sourcePath}");
        }

        $sourceUrl = $this->resolveSourceUrl($storage, $sourcePath);

        [$prompt, $negative] = $this->buildPrompts($job);
        [$targetW, $targetH] = $this->resolveTargetDimensions($storage, $sourcePath);

        if ($this->isSdxlModel()) {
            if (! $this->version) {
                throw new RuntimeException('Replicate model version is required for SDXL requests.');
            }

            $input = [
                'image_input' => [$sourceUrl],
                'prompt' => $prompt,
                'negative_prompt' => $negative,
                'strength' => $this->strength,
                'guidance_scale' => $this->guidanceScale,
                'steps' => $this->steps,
                'width' => $targetW,
                'height' => $targetH,
            ];

            $payload = [
                'version' => $this->version,
                'input' => $input,
            ];
            $endpoint = 'predictions';
        } else {
            $input = [
                'size' => $this->size,
                'prompt' => $prompt,
                'max_images' => $this->maxImages,
                'image_input' => [$sourceUrl],
                'aspect_ratio' => 'match_input_image',
                'sequential_image_generation' => 'disabled',
                'width' => $targetW,
                'height' => $targetH,
            ];

            if ($negative !== '') {
                $input['negative_prompt'] = $negative;
            }

            $payload = ['input' => $input];
            $endpoint = sprintf('models/%s/predictions', $this->modelSlug());
        }

        $response = $this->http
            ->withHeaders(['Prefer' => $this->waitPreference])
            ->post($endpoint, $payload);

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'Replicate request failed (%s %s): %s',
                $response->status(),
                $endpoint,
                $response->body(),
            ));
        }

        $resultUrl = $this->extractResultUrl($response->json());

        $imageResponse = Http::timeout(120)->get($resultUrl);
        if ($imageResponse->failed()) {
            throw new RuntimeException(sprintf('Unable to download Replicate output: %s', $resultUrl));
        }

        $extension = $this->guessExtension($resultUrl);

        $targetPath = sprintf(
            'glowup/%d/after/%s.%s',
            $job->property_id,
            Str::uuid(),
            $extension,
        );

        $storage->put($targetPath, $imageResponse->body());

        return $targetPath;
    }

    private function buildPrompts(GlowupJob $job): array
    {
        $room = $this->humanize($job->room_type, 'space');
        $style = $this->humanize($job->style, 'modern');

        if ($this->isSdxlModel()) {
            $template = $this->promptTemplateSdxl ?: 'Enhance the provided photo with subtle, realistic upgrades.';
            $prompt = str_replace(
                ['{room}', '{style}', '{property_id}'],
                [$room, $style, (string) $job->property_id],
                $template,
            );
            $negative = trim($this->negativePromptTemplateSdxl);

            if ($negative === '') {
                $negative =
                    'new layout, new room, altered geometry, incorrect perspective, '
                    . 'warped walls, moved furniture, extra objects, missing objects, '
                    . 'fantasy, CGI look, cartoon, illustration, dramatic redesign';
            }

            return [trim($prompt), $negative];
        }

        $template = $this->promptTemplateSeedream
            ?: 'Edit the provided reference photo of a {room}. Apply a tasteful {style} glow-up.';

        $prompt = str_replace(
            ['{room}', '{style}', '{property_id}'],
            [$room, $style, (string) $job->property_id],
            $template,
        );

        return [trim($prompt), ''];
    }

    private function resolveTargetDimensions($storage, string $path): array
    {
        [$w, $h] = $this->readImageSize($storage, $path);
        $maxEdge = max(1, (int) max($this->width, $this->height));
        $originalMax = max(1, (int) max($w, $h));
        $longEdge = min($maxEdge, $originalMax);

        return $this->fitToLongEdge($w, $h, $longEdge);
    }

    private function readImageSize($storage, string $path): array
    {
        if (method_exists($storage, 'path')) {
            $abs = $storage->path($path);
            $info = @getimagesize($abs);
            if (is_array($info) && isset($info[0], $info[1])) {
                return [(int) $info[0], (int) $info[1]];
            }
        }

        $bytes = $storage->get($path);
        $info = @getimagesizefromstring($bytes);
        if (is_array($info) && isset($info[0], $info[1])) {
            return [(int) $info[0], (int) $info[1]];
        }

        return [1600, 1200];
    }

    private function fitToLongEdge(int $w, int $h, int $longEdge): array
    {
        if ($w <= 0 || $h <= 0) {
            return [$longEdge, (int) round($longEdge * 0.75)];
        }

        if ($w >= $h) {
            $ratio = $h / $w;
            return [$longEdge, (int) round($longEdge * $ratio)];
        }

        $ratio = $w / $h;
        return [(int) round($longEdge * $ratio), $longEdge];
    }

    private function isSdxlModel(): bool
    {
        return str_contains(strtolower($this->model), 'sdxl');
    }

    private function resolveSourceUrl($storage, string $path): string
    {
        if (method_exists($storage, 'temporaryUrl')) {
            return $storage->temporaryUrl($path, now()->addMinutes(10));
        }

        return $storage->url($path);
    }

    private function humanize(?string $value, string $fallback): string
    {
        if (! $value) return $fallback;
        return Str::of($value)->replace('_', ' ')->lower()->toString(); // 👈 evita Headline/TitleCase raro
    }

    private function extractResultUrl(array $response): string
    {
        $output = $response['output'] ?? null;

        if (is_string($output) && filter_var($output, FILTER_VALIDATE_URL)) {
            return $output;
        }

        if (is_array($output) && isset($output[0]) && filter_var($output[0], FILTER_VALIDATE_URL)) {
            return $output[0];
        }

        throw new RuntimeException('Replicate did not return a valid output URL.');
    }

    private function guessExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo((string) $path, PATHINFO_EXTENSION);

        return $extension ?: 'jpg';
    }

    private function modelSlug(): string
    {
        $model = trim($this->model, '/');

        if (str_contains($model, '/')) return $model;

        $owner = trim($this->modelOwner ?? '', '/');
        if ($owner === '') {
            throw new RuntimeException('Replicate model owner is not configured.');
        }

        return "{$owner}/{$model}";
    }
}
