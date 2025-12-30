<?php

/**
 * Description: File delivering a mock worth provider returning deterministic valuation data.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Enables local development and tests without hitting external APIs.
 */

namespace App\Infrastructure\Property\PixrWorth\Providers;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\Contracts\WorthProvider;
use App\Domain\Properties\Entities\PropertyEntity;
use Carbon\Carbon;

/**
 * Description: Mock worth provider supplying canned responses for PixrWorth flows.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Allows the application to function offline with predictable valuation output.
 */
class MockWorthProvider implements WorthProvider
{
    private const PROVIDER_NAME = 'mock';

    /**
     * Description: Fetch property valuation data from provider.
     * Parameters: Property $property Property model used to tailor mock response.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns static valuation data emulating provider output.
     */
    public function appraisal(PropertyEntity $property): PropertyWorthDTO
    {
        $baseValue = 485000;
        $offset = ($property->id ?? 0) % 1000;
        $adjusted = $baseValue + ($offset * 10);
        $timestamp = Carbon::now();

        return new PropertyWorthDTO(
            value: (float) $adjusted,
            value_low: (float) ($adjusted * 0.97),
            value_high: (float) ($adjusted * 1.03),
            confidence: 0.87,
            comparables: [
                [
                    'address' => '120 Oak St',
                    'sale_price' => 490000,
                    'distance_miles' => 0.4,
                ],
                [
                    'address' => '98 Pine Ave',
                    'sale_price' => 475000,
                    'distance_miles' => 0.7,
                ],
            ],
            provider: self::PROVIDER_NAME,
            fetched_at: $timestamp,
            cached_at: null,
        );
    }
}
