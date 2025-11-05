<?php

/**
 * Description: Event emitted whenever an application feature usage is recorded.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Provides payload for listeners to react to feature consumption.
 */

namespace App\Domain\Shared\Events;

/**
 * Description: Carries metadata describing a feature usage occurrence.
 * Parameters: int $userId Identifier for the user; string $feature Feature name; int $quantity Units consumed.
 * Returns: Not applicable.
 * Expected Result: Listeners can update persistent counters or analytics.
 */
class FeatureUsed
{
    /**
     * Description: Instantiate the feature usage event payload.
     * Parameters: int $userId User identifier triggering the usage; string $feature Feature key; int $quantity Units consumed in this event.
     * Returns: void.
     * Expected Result: Event encapsulates usage details for downstream listeners.
     */
    public function __construct(
        public readonly int $userId,
        public readonly string $feature,
        public readonly int $quantity = 1,
    ) {
    }
}
