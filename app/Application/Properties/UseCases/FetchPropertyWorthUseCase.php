<?php

/**
 * Description: File defining the FetchPropertyWorthUseCase orchestrating PixrWorth retrieval.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Exposes a use case to drive backend controller interactions.
 */

namespace App\Application\Properties\UseCases;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\Services\AppraisalService;
use App\Domain\Shared\Exceptions\FeatureLimitExceededException;
use App\Models\Property;

/**
 * Description: Use case coordinating property worth fetching via the application service.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Controllers invoke this use case to obtain valuation DTOs for responses.
 */
readonly class FetchPropertyWorthUseCase
{
    /**
     * Description: Build the use case with its supporting service.
     * Parameters: AppraisalService $service Service performing valuation retrieval and persistence.
     * Returns: void.
     * Expected Result: Use case ready to serve controller or job invocations.
     */
    public function __construct(
        private AppraisalService $service,
    ) {
    }

    /**
     * Description: Execute the use case returning valuation data for the supplied property.
     * Parameters: Property $property Property instance to evaluate.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns DTO produced by the application service for the property.
     * @throws FeatureLimitExceededException|\Throwable
     */
    public function execute(Property $property): PropertyWorthDTO
    {
        return $this->service->fetchValuation($property);
    }
}
