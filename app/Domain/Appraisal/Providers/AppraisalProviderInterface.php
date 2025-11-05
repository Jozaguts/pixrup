<?php

/**
 * Description: File describing the AppraisalProviderInterface abstraction for valuation providers.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Allows application services to depend on provider contracts instead of implementations.
 */

namespace App\Domain\Appraisal\Providers;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Models\Property;

/**
 * Description: Contract for external valuation providers supplying property worth data.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Implementations fetch and normalize appraisal payloads from their respective APIs.
 */
interface AppraisalProviderInterface
{
    /**
     * Description: Fetch property valuation data from the underlying provider.
     * Parameters: Property $property Eloquent property model containing location metadata.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns normalized valuation payload ready for application processing.
     */
    public function fetchValue(Property $property): PropertyWorthDTO;
}
