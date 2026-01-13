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
    private string $negativePromptTemplate;
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

        $baseUrl = rtrim(config('services.replicate.base_url', 'https://api.replicate.com/v1/'), '/') . '/';

        $this->http = Http::withToken($token)
            ->acceptJson()
            ->baseUrl($baseUrl)
            ->timeout((int) config('services.replicate.timeout', 120))
            ->connectTimeout(10)
            ->retry((int) config('services.replicate.retries', 2), 2000);

        $this->modelOwner = config('services.replicate.model_owner');
        $this->model = trim(config('services.replicate.model', 'seedream-4'));
        $this->promptTemplate = (string) config('services.replicate.prompt_template');
        $this->negativePromptTemplate = (string) config('services.replicate.negative_prompt_template', '');
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

        // 👇 Dimensiones reales (si el disco es local/temporalmente accesible)
        // Si estás en S3 y no tienes path local, puedes usar getimagesizefromstring($storage->get()) (ojo RAM).
        [$w, $h] = $this->readImageSize($storage, $sourcePath);

        // Target long edge (mantén 2048 si quieres) y preserva aspect
        [$targetW, $targetH] = $this->fitToLongEdge($w, $h, 2048);

        [$prompt, $negative] = $this->buildPrompts($job);

        $endpoint = sprintf('models/%s/predictions', $this->modelSlug());

        $input = [
            'size' => $this->size,
            'prompt' => $prompt,
            'max_images' => $this->maxImages,
            'image_input' => [$sourceUrl],
            'aspect_ratio' => $this->aspectRatio,
            'sequential_image_generation' => 'disabled',
            // 👇 respeta geometría (NO fuerces 2048x2048)
            'width' => $targetW,
            'height' => $targetH,
        ];

        // 👇 Si el modelo soporta negative_prompt, úsalo (si no, no rompe nada si lo ignora; pero mejor loguear)
        if ($negative !== '') {
            $input['negative_prompt'] = $negative;
        }

        $payload = ['input' => $input];

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

    private function readImageSize($storage, string $path): array
    {
        // Si es filesystem local, puedes usar ->path()
        if (method_exists($storage, 'path')) {
            $abs = $storage->path($path);
            $info = @getimagesize($abs);
            if (is_array($info) && isset($info[0], $info[1])) {
                return [(int) $info[0], (int) $info[1]];
            }
        }

        // Fallback: lee bytes (ojo en imágenes grandes)
        $bytes = $storage->get($path);
        $info = @getimagesizefromstring($bytes);
        if (is_array($info) && isset($info[0], $info[1])) {
            return [(int) $info[0], (int) $info[1]];
        }

        // Si no pudimos leer, usa default no-cuadrado
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

    private function buildPrompts(GlowupJob $job): array
    {
        $customPrompt = data_get($job->meta, 'prompt');
        $customNegative = data_get($job->meta, 'negative_prompt');

        $room = $this->humanize($job->room_type, 'space');
        $style = $this->humanize($job->style, 'modern');

        $prompt = '';
        $negative = '';

        if (is_string($customPrompt) && trim($customPrompt) !== '') {
            $prompt = trim($customPrompt);
        } else {
            $template = $this->promptTemplate ?: 'Transform the reference photo of a {room} into {style}. Preserve perspective and layout.';
            $prompt = str_replace(['{room}', '{style}', '{property_id}'], [$room, $style, (string) $job->property_id], $template);
        }

        if (is_string($customNegative) && trim($customNegative) !== '') {
            $negative = trim($customNegative);
        } else {
            $negative = trim($this->negativePromptTemplate);
        }

        return [$prompt, $negative];
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
