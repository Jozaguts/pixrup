<?php

namespace App\Domain\Properties\Entities;

class PropertyEntity
{
    public function __construct(
        public ?int $id = null,
        public string $title,
        public ?string $status,
        public string $address,
        public string $city,
        public string $state,
        public string $postal_code,
        public string $country,
        public float $lat,
        public float $lng,
        public string $place_id,
        public mixed $metadata,
    ) {
    }
}