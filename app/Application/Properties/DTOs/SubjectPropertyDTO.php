<?php

namespace App\Application\Properties\DTOs;

class SubjectPropertyDTO
{
    public function __construct(
    public readonly string $type,
    public readonly int $bedrooms,
    public readonly int $bathrooms,
    public readonly int $squareFootage)
    {}
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'squareFootage' => $this->squareFootage,
        ];
    }
}