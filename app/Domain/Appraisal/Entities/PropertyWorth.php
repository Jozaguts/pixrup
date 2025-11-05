<?php

/**
 * Description: File declaring the PropertyWorth domain entity representing a persisted valuation.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Exposes the PropertyWorth entity for domain-level operations.
 */

namespace App\Domain\Appraisal\Entities;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use Carbon\Carbon;

/**
 * Description: Domain entity describing a property worth snapshot assembled from provider data.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Encapsulates valuation attributes independent from infrastructure concerns.
 */
class PropertyWorth
{
    /**
     * Description: Build an immutable domain entity for property valuation data.
     * Parameters: float $value Average valuation; float $valueLow Lower bound; float $valueHigh Upper bound; float $confidence Confidence score; array $comparables Comparable sales; string $provider Provider identifier; Carbon $fetchedAt Source timestamp; Carbon $createdAt Persistence timestamp; Carbon $updatedAt Persistence update timestamp.
     * Returns: void.
     * Expected Result: Domain entity mirrors stored valuation values.
     */
    public function __construct(
        public readonly float $value,
        public readonly float $value_low,
        public readonly float $value_high,
        public readonly float $confidence,
        public readonly array $comparables,
        public readonly string $provider,
        public readonly Carbon $fetched_at,
        public readonly Carbon $created_at,
        public readonly Carbon $updated_at,
    ) {
    }

    /**
     * Description: Transform the domain entity into a PropertyWorthDTO for application consumption.
     * Parameters: ?Carbon $cachedAt Timestamp describing when value was cached, if applicable.
     * Returns: PropertyWorthDTO
     * Expected Result: DTO contains identical values to entity enabling transport across layers.
     */
    public function toDto(?Carbon $cachedAt = null): PropertyWorthDTO
    {
        return new PropertyWorthDTO(
            value: $this->value,
            value_low: $this->value_low,
            value_high: $this->value_high,
            confidence: $this->confidence,
            comparables: $this->comparables,
            provider: $this->provider,
            fetched_at: $this->fetched_at,
            cached_at: $cachedAt,
        );
    }
}
