<?php

namespace App\Services\Appraisal;

use App\Events\PropertyWorthReady;
use App\Models\Property;
use App\Models\PropertyWorth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PropertyWorthService
{
    /**
     * Fetch an appraisal for the given property, persist it, and broadcast readiness.
     */
    public function fetch(Property $property): PropertyWorth
    {
        $payload = $this->requestValuation($property);

        $worth = new PropertyWorth([
            'value' => $payload['value'],
            'confidence' => $payload['confidence'],
            'comparables' => $payload['comparables'],
            'trend' => $payload['trend'],
            'provider' => $payload['provider'],
            'fetched_at' => now(),
        ]);

        $property->worths()->save($worth);

        PropertyWorthReady::dispatch($property, $worth);

        return $worth->fresh();
    }

    /**
     * Simulate the external valuation request while logging the attempt.
     *
     * Replace this with an injected API client once the provider contract is ready.
     *
     * @return array{
     *     value: float,
     *     confidence: int,
     *     comparables: array<int, array<string, mixed>>,
     *     trend: array<int, array<string, mixed>>,
     *     provider: string,
     * }
     */
    protected function requestValuation(Property $property): array
    {
        Log::info('Requesting property worth valuation', [
            'property_id' => $property->id,
            'address' => $property->address,
            'lat' => $property->lat,
            'lng' => $property->lng,
        ]);

        $baseValue = $this->estimateBaseline($property);
        $confidence = random_int(78, 94);
        $trendPoints = $this->generateTrend($baseValue);
        $comparables = $this->generateComparables($property, $baseValue);

        return [
            'value' => round($baseValue, 2),
            'confidence' => $confidence,
            'comparables' => $comparables,
            'trend' => $trendPoints,
            'provider' => 'housecanary',
        ];
    }

    protected function estimateBaseline(Property $property): float
    {
        $hash = crc32((string) $property->id.$property->address);
        $seed = ($hash % 250_000) + 350_000;

        return (float) ($seed + random_int(-20_000, 45_000));
    }

    protected function generateTrend(float $baseValue): array
    {
        $collection = Collection::times(6, function (int $index) use ($baseValue): array {
            $offset = ($index - 5) * 15;
            $value = $baseValue * (1 + ($offset / 1000));

            return [
                'label' => now()->subDays((5 - $index) * 15)->format('M d'),
                'value' => round($value, 2),
            ];
        });

        return $collection->values()->all();
    }

    protected function generateComparables(Property $property, float $baseValue): array
    {
        $city = $property->city ?: 'Austin';
        $state = $property->state ?: 'TX';

        return Collection::times(3, function (int $index) use ($city, $state, $baseValue): array {
            $adjusted = $baseValue * (1 + (($index - 1) * 0.018));
            $distance = number_format(($index + 1) * 0.35, 1);

            return [
                'id' => (string) Str::uuid(),
                'address' => sprintf('%d %s Ave, %s, %s', random_int(101, 999), $this->streetName(), $city, $state),
                'distance' => "{$distance} mi",
                'price' => round($adjusted),
                'delta' => sprintf('%+.1f%%', ($adjusted - $baseValue) / $baseValue * 100),
            ];
        })->values()->all();
    }

    protected function streetName(): string
    {
        $streets = ['Willow', 'Maple', 'Cedar', 'Walnut', 'Lamar', 'Monroe'];

        return $streets[array_rand($streets)];
    }
}
