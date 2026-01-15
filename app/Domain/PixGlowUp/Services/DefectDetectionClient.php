<?php

namespace App\Domain\PixGlowUp\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;

class DefectDetectionClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $baseUrl = rtrim(config('services.replicate.base_url', 'https://api.replicate.com/v1/'), '/') . '/';

        $this->client = Http::withToken(config('services.replicate.token'))
            ->acceptJson()
            ->baseUrl($baseUrl)
            ->timeout((int) config('services.replicate.timeout', 120))
            ->connectTimeout(10)
            ->retry((int) config('services.replicate.retries', 2), 2000);
    }

    /**
     * @throws ConnectionException
     */
    public function createPrediction(string $s3ImageUrl): string
    {
        $response = $this->client->post('predictions', [
            'version' => 'kojott/content-moderation-vision:f029685df7399a35ba89d395c4bb1cf1c53542461cd289ac3f1069e76e6b3470',
            'input' => [
                'image' => $s3ImageUrl,
                'prompt' => $this->buildPrompt(),
                'temperature' => 0.1,
                'top_p' => 0.9,
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('PixGlowUp vision request failed');
        }

        $predictionId = $response->json('id');

        if(! $predictionId) {
            throw new RuntimeException('PixGlowUp vision response missing prediction URL');
        }

        return $predictionId;
    }

    /**
     * Fetch prediction result from Replicate polling URL
     *
     * @throws ConnectionException
     * @throws JsonException
     */
    public function fetchPredictionResult(string $predictionId): array
    {
        $response = $this->client->get("predictions/{$predictionId}");

        if (! $response->successful()) {
            throw new RuntimeException('PixGlowUp prediction fetch failed');
        }
        $status = $response->json('status');

        if ($status === 'failed') {
            throw new RuntimeException('PixGlowUp vision prediction failed');
        }

        if ($status !== 'succeeded') {
            return [
                'status' => $status,
                'output' => null,
            ];
        }

        $output = $response->json('output');

        if (! $output) {
            throw new RuntimeException('PixGlowUp prediction succeeded but output missing');
        }

        return [
            'status' => 'succeeded',
            'output' => $this->normalizeOutput($output),
        ];
    }
    protected function buildPrompt(): string
    {
        return <<<PROMPT
            Return ONLY valid JSON.
            You are a visual property inspection system.
            Detect ONLY clearly visible physical defects.
            Do NOT infer hidden issues.
            Ignore minor cosmetic or aesthetic imperfections (small scuffs, paint wear, minor stains).
            Only report defects that would reasonably impact property value or require repair.
            For each defect:
            - "detected": true only if clearly visible
            - "severity":
                - "minor"  → cosmetic, no structural concern
                - "moderate" → repair needed, localized
                - "severe" → structural risk or widespread damage
            - "confidence": number between 0 and 1 representing visual certainty
            If a defect is not visible, set detected=false and severity=null.
            {
              "water_damage": {
                "detected": boolean,
                "severity": "minor" | "moderate" | "severe" | null,
                "confidence": 0-1
              },
              "mold": {
                "detected": boolean,
                "severity": "moderate" | "severe" | null,
                "confidence": 0-1
              },
              "termite_damage": {
                "detected": boolean,
                "severity": "moderate" | "severe" | null,
                "confidence": 0-1
              },
              "wall_cracks": {
                "detected": boolean,
                "severity": "minor" | "moderate" | "severe" | null,
                "confidence": 0-1
              },
              "ceiling_damage": {
                "detected": boolean,
                "severity": "moderate" | "severe" | null,
                "confidence": 0-1
              },
              "broken_windows": {
                "detected": boolean,
                "severity": "moderate" | "severe" | null,
                "confidence": 0-1
              },
              "roof_visible_damage": {
                "detected": boolean,
                "severity": "moderate" | "severe" | null,
                "confidence": 0-1
              }
            }
        PROMPT;
    }

    /**
     * @throws JsonException
     */
    protected function normalizeOutput(mixed $output): array
    {
        if (is_array($output)) {
            $output = implode("\n", $output);
        }

        $output = trim($output);

        $decoded = json_decode($output, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('PixGlowUp returned invalid JSON');
        }

        return $decoded;
    }
}
