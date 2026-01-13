<?php

/**
 * Description: File registering the PropertyWorthController handling PixrWorth requests.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Provides controller entry point for fetching property valuations via JSON.
 */

namespace App\Interface\Appraisal\Http\Controllers;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\UseCases\AppraisePropertyWorthUseCase;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Description: Controller responsible for orchestrating PixrWorth fetch operations.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Returns JSON responses containing valuation data or errors.
 */
class PropertyWorthController extends Controller
{
    /**
     * Description: Fetch and return property valuation (mock or real).
     * Parameters: Property $property Target property; AppraisePropertyWorthUseCase $useCase Use case executing valuation logic.
     * Returns: JsonResponse
     * Expected Result: Responds with JSON payload containing valuation data or error details.
     */
    public function fetch(
        Property $property,
        AppraisePropertyWorthUseCase $useCase
    ): JsonResponse {
        try {
            $dto = $useCase->execute($property->toEntity());

            return response()->json([
                'worth' => $this->transformDto($dto),
            ]);
        } catch (FeatureLimitExceededException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'limit',
            ], 403);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'We couldn’t retrieve data. Please try again later.',
                'code' => 'error',
            ], 500);
        }
    }

    /**
     * Description: Convert valuation DTO into an array suitable for JSON payloads.
     * Parameters: PropertyWorthDTO $dto Valuation result to transform.
     * Returns: array<string, mixed>
     * Expected Result: Provides serializable payload for frontend consumption.
     */
    private function transformDto(PropertyWorthDTO $dto): array
    {
        return [
            'value' => $dto->value,
            'value_low' => $dto->value_low,
            'value_high' => $dto->value_high,
            'confidence' => $dto->confidence,
            'comparables' => $dto->comparables,
            'provider' => $dto->provider,
            'fetched_at' => $dto->fetched_at->toIso8601String(),
            'cached_at' => $dto->cached_at?->toIso8601String(),
        ];
    }

}
