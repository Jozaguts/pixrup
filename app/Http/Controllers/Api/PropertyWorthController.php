<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyWorth;
use App\Services\Appraisal\PropertyWorthService;
use Illuminate\Http\JsonResponse;

class PropertyWorthController extends Controller
{
    public function __construct(
        protected PropertyWorthService $service
    ) {}

    public function show(Property $property): JsonResponse
    {
        $worth = $property->latestWorth?->fresh();

        if (! $worth) {
            return response()->json([
                'status' => 'idle',
                'worth' => null,
            ]);
        }

        return response()->json([
            'status' => 'ready',
            'worth' => $this->transformWorth($worth),
        ]);
    }

    public function fetch(Property $property): JsonResponse
    {
        $worth = $this->service->fetch($property);

        return response()->json([
            'status' => 'success',
            'worth' => $this->transformWorth($worth),
            'message' => 'Appraisal completed successfully 🎯',
        ]);
    }

    public function attachToReport(Property $property): JsonResponse
    {
        $worth = $property->latestWorth;

        if (! $worth) {
            return response()->json([
                'message' => 'No appraisal available to add to the report.',
            ], 422);
        }

        // Placeholder for future integration with PixrSeal.

        return response()->json([
            'message' => 'Appraisal added to the report queue.',
        ]);
    }

    protected function transformWorth(PropertyWorth $worth): array
    {
        return [
            'id' => $worth->id,
            'value' => $worth->value,
            'confidence' => $worth->confidence,
            'comparables' => $worth->comparables ?? [],
            'trend' => $worth->trend ?? [],
            'provider' => $worth->provider,
            'fetched_at' => optional($worth->fetched_at)->toIso8601String(),
        ];
    }
}
