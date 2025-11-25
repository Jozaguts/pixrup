<?php

namespace App\Application\Properties\DTOs;

class ValueEstimateDTO
{
    public function __construct(
        public ?float $price,
        public ?float $rangeLow,
        public ?float $rangeHigh,
    ) {}
    public function toArray(): array
    {
        return [
            'price' => $this->price,
            'range_low' => $this->rangeLow,
            'range_high' => $this->rangeHigh,
        ];
    }
}