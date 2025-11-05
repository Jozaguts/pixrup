<?php

/**
 * Description: File defining the HouseCanaryProvider placeholder adapter for future integration.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Allows dependency injection to resolve even before real API wiring.
 */

namespace App\Infrastructure\Appraisal\Providers;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Models\Property;
use Carbon\Carbon;

/**
 * Description: HouseCanary provider stub returning deterministic data until real API access is configured.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Enables non-mock configuration without runtime failures during initial development.
 */
class HouseCanaryProvider implements AppraisalProviderInterface
{
    private const PROVIDER_NAME = 'housecanary';

    /**
     * Description: Fetch property valuation data from provider.
     * Parameters: Property $property Property model used to seed placeholder values.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns predictable placeholder data indicating unimplemented provider workflow.
     */
    public function fetchValue(Property $property): PropertyWorthDTO
    {
        $timestamp = Carbon::now();

        return new PropertyWorthDTO(
            value: 0.0,
            value_low: 0.0,
            value_high: 0.0,
            confidence: 0.0,
            comparables: [],
            provider: self::PROVIDER_NAME,
            fetched_at: $timestamp,
            cached_at: null,
        );
    }
}
