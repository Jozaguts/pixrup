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

    private string $promptTemplate;

    private string $size;

    private string $aspectRatio;

    private int $maxImages;

    private string $waitPreference;

    public function __construct()
    {
        $token = config('services.replicate.token');

        if (! $token) {
            throw new RuntimeException('Missing Replicate API token.');
        }

        $baseUrl = rtrim(
            config('services.replicate.base_url', 'https://api.replicate.com/v1/'),
            '/',
        ).'/';

        $this->http = Http::withToken($token)
            ->acceptJson()
            ->baseUrl($baseUrl);

        $this->modelOwner = config('services.replicate.model_owner');
        $this->model = trim(config('services.replicate.model', 'seedream-4'));
        $this->promptTemplate = config('services.replicate.prompt_template');
        $this->size = config('services.replicate.size', '2K');
        $this->aspectRatio = config('services.replicate.aspect_ratio', '4:3');
        $this->maxImages = (int) config('services.replicate.max_images', 1);
        $this->waitPreference = config('services.replicate.wait_preference', 'wait=60');
    }

    public function generate(GlowupJob $job, string $sourcePath, string $disk): string
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($sourcePath)) {
            throw new RuntimeException("Source image not found: {$sourcePath}");
        }

        $sourceUrl = $this->resolveSourceUrl($storage, $sourcePath);

        $prompt = $this->buildPrompt($job);

        $endpoint = sprintf('models/%s/predictions', $this->modelSlug());

        $payload = [
            'input' => [
                'size' => $this->size,
                'width' => 2048,
                'height' => 2048,
                'prompt' => $prompt,
                'max_images' => $this->maxImages,
                'image_input' => [$sourceUrl],
                'aspect_ratio' => $this->aspectRatio,
                'sequential_image_generation' => 'disabled',
            ],
        ];

        $response = $this->http
            ->withHeaders(['Prefer' => $this->waitPreference])
            ->post($endpoint, $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                sprintf(
                    'Replicate request failed (%s %s): %s',
                    $response->status(),
                    $endpoint,
                    $response->body(),
                ),
            );
        }

        $resultUrl = $this->extractResultUrl($response->json());

        $imageResponse = Http::timeout(120)->get($resultUrl);

        if ($imageResponse->failed()) {
            throw new RuntimeException(
                sprintf('Unable to download Replicate output: %s', $resultUrl),
            );
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

    private function resolveSourceUrl($storage, string $path): string
    {
        if (method_exists($storage, 'temporaryUrl')) {
            return $storage->temporaryUrl($path, now()->addMinutes(10));
        }

        return $storage->url($path);
    }

    private function buildPrompt(GlowupJob $job): string
    {
        $customPrompt = data_get($job->meta, 'prompt');

        if (is_string($customPrompt) && trim($customPrompt) !== '') {
            return trim($customPrompt);
        }

        $room = $this->humanize($job->room_type, 'space');
        $style = $this->humanize($job->style, 'modern');

        $template = $this->promptTemplate ?: 'Photo of a {room} styled as {style}';

        return str_replace(
            ['{room}', '{style}', '{property_id}'],
            [$room, $style, (string) $job->property_id],
            $template,
        );
    }

    private function humanize(?string $value, string $fallback): string
    {
        if (! $value) {
            return $fallback;
        }

        return Str::of($value)->replace('_', ' ')->headline();
    }

    /**
     * @param array<string, mixed> $response
     */
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

        if (str_contains($model, '/')) {
            return $model;
        }

        $owner = trim($this->modelOwner ?? '', '/');

        if ($owner === '') {
            throw new RuntimeException('Replicate model owner is not configured.');
        }

        return "{$owner}/{$model}";
    }
}
