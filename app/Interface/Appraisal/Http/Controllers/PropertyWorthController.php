<?php

/**
 * Description: File registering the PropertyWorthController handling PixrWorth requests.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Provides controller entry point for fetching property valuations via Inertia.
 */

namespace App\Interface\Appraisal\Http\Controllers;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Application\Appraisal\UseCases\FetchPropertyWorthUseCase;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Description: Controller responsible for orchestrating PixrWorth fetch operations.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Returns Inertia responses containing valuation data or errors.
 */
class PropertyWorthController extends Controller
{
    /**
     * Description: Fetch and return property valuation (mock or real).
     * Parameters: Request $request Incoming HTTP request; Property $property Target property; FetchPropertyWorthUseCase $useCase Use case executing valuation logic.
     * Returns: InertiaResponse|\Symfony\Component\HttpFoundation\Response
     * Expected Result: Responds with Inertia component containing valuation data or error details.
     */
    public function fetch(
        Request $request,
        Property $property,
        FetchPropertyWorthUseCase $useCase
    ): \Symfony\Component\HttpFoundation\Response {
        try {
            $dto = $useCase->execute($property);

            /** @var InertiaResponse $inertia */
            $inertia = Inertia::render('Appraisal/PixrWorth', [
                'worth' => $this->transformDto($dto),
                'property' => $this->transformProperty($property),
            ]);

            return $inertia->toResponse($request);
        } catch (FeatureLimitExceededException $exception) {
            /** @var InertiaResponse $inertia */
            $inertia = Inertia::render('Appraisal/PixrWorth', [
                'worth' => null,
                'property' => $this->transformProperty($property),
                'errors' => [
                    'worth' => $exception->getMessage(),
                ],
                'usage' => $exception->context(),
            ]);

            return $inertia->toResponse($request)->setStatusCode(403);
        }
    }

    /**
     * Description: Convert valuation DTO into an array suitable for Inertia props.
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

    /**
     * Description: Normalize property model data for frontend consumption.
     * Parameters: Property $property Property model bound by the route.
     * Returns: array<string, mixed>
     * Expected Result: Provides lean property payload for PixrWorth Vue page.
     */
    private function transformProperty(Property $property): array
    {
        return [
            'id' => $property->getKey(),
            'title' => $property->title,
            'address' => $property->address,
            'city' => $property->city,
            'state' => $property->state,
            'postal_code' => $property->postal_code,
            'country' => $property->country,
        ];
    }
}
