<?php

namespace App\Application\Properties\DTOs;

class ValueEstimateDTO
{
    public function __construct(
        public float $estimate,
        public float $rangeLow,
        public float $rangeHigh,
        public float $confidence,
    ) {}
    public function toArray(): array
    {
        return [
            'estimate' => $this->estimate,
            'rangeLow' => $this->rangeLow,
            'rangeHigh' => $this->rangeHigh,
            'confidence' => $this->confidence,
        ];
    }
}