<?php

/**
 * Description: File describing the AppraisalProviderInterface abstraction for valuation providers.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Allows application services to depend on provider contracts instead of implementations.
 */

namespace App\Domain\Appraisal\Providers;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Domain\Properties\Entities\PropertyEntity;

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
     * Parameters: Property $property  property entity containing location metadata.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns normalized valuation payload ready for application processing.
     */
    public function fetchValue(PropertyEntity $property): PropertyWorthDTO;

    public function detailsAdvanced(PropertyEntity $property);
    public function census(PropertyEntity $property);
    public function salesHistory(PropertyEntity $property);
    public function ownerOccupied(PropertyEntity $property);
    public function femaDisasterArea(PropertyEntity $property);
    public function flood(PropertyEntity $property);
    public function blockCrime(PropertyEntity $property);
    public function marketPulse(PropertyEntity $property, string $type = 'latest');

}
