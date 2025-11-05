<?php

/**
 * Description: File defining the PropertyWorthDTO used to transfer valuation data between layers.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Provides a strongly typed DTO for property worth responses.
 */

namespace App\Application\Appraisal\DTOs;

use Carbon\Carbon;

/**
 * Description: Data Transfer Object for property valuation results.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Instances encapsulate normalized appraisal data for downstream consumers.
 */
class PropertyWorthDTO
{
    /**
     * Description: Instantiate the DTO with normalized property worth values.
     * Parameters: float $value Average valuation; float $valueLow Lower bound; float $valueHigh Upper bound; float $confidence Confidence score; array $comparables Comparable sales; string $provider Provider name; Carbon $fetchedAt Timestamp returned by provider; ?Carbon $cachedAt Cache timestamp when applicable.
     * Returns: void.
     * Expected Result: DTO properties are populated for transport across layers.
     */
    public function __construct(
        public float $value,
        public float $value_low,
        public float $value_high,
        public float $confidence,
        public array $comparables,
        public string $provider,
        public Carbon $fetched_at,
        public ?Carbon $cached_at = null,
    ) {
    }
}
