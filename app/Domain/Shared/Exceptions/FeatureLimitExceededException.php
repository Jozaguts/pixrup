<?php

/**
 * Description: File declaring FeatureLimitExceededException for plan usage enforcement.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Makes custom exception available for feature limit violations.
 */

namespace App\Domain\Shared\Exceptions;

use Exception;

/**
 * Description: Exception thrown when a user exceeds the allowed usage for a feature.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Consumers can detect feature limit breaches and respond accordingly.
 */
class FeatureLimitExceededException extends Exception
{
    public function __construct(
        string $message = '',
        private readonly array $context = [],
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function context(): array
    {
        return $this->context;
    }
}
