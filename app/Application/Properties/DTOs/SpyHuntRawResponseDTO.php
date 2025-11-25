<?php

namespace App\Application\Properties\DTOs;

class SpyHuntRawResponseDTO
{
    public function __construct(
        public array $subjectProperty,
        public array $valueEstimate,
        public array $saleComps,
        public ?array $rentEstimate,
        public ?array $rentComps,
        public array $rawValueResponse,
        public ?array $rawRentResponse,
    ) {}
}