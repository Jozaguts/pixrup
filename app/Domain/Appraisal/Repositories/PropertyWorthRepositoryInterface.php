<?php

/**
 * Description: File defining the PropertyWorthRepositoryInterface contract for persistence operations.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Establishes abstraction for storing and retrieving property worth data.
 */

namespace App\Domain\Appraisal\Repositories;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Domain\Appraisal\Entities\PropertyWorth;
use Carbon\Carbon;

/**
 * Description: Repository port for interacting with property worth persistence mechanisms.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Adapters implement this interface to provide storage access for valuations.
 */
interface PropertyWorthRepositoryInterface
{
    /**
     * Description: Locate the most recent valuation within the provided freshness threshold.
     * Parameters: int $propertyId Identifier for the property; Carbon $threshold Earliest acceptable fetched_at timestamp.
     * Returns: ?PropertyWorth
     * Expected Result: Returns a domain entity if a fresh valuation exists, otherwise null.
     */
    public function findLatestWithin(int $propertyId, Carbon $threshold): ?PropertyWorth;

    /**
     * Description: Persist valuation data for the supplied property using DTO values.
     * Parameters: int $propertyId Identifier for the property; PropertyWorthDTO $dto Normalized valuation payload.
     * Returns: PropertyWorth
     * Expected Result: Database reflects the new valuation and returns it as a domain entity.
     */
    public function saveFromDto(int $propertyId, PropertyWorthDTO $dto): PropertyWorth;
}
