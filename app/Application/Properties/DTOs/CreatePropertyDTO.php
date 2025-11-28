<?php
namespace App\Application\Properties\DTOs;

class CreatePropertyDTO
{
    public function __construct(
        public string $user_id,
        public string $title,
        public ?string $status,
        public string $address,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country,
        public float $lat,
        public float $lng,
        public ?string $place_id,
        public mixed $metadata,
        public string $property_type,
        public int $bedrooms,
        public int $bathrooms,
        public int $square_footage
    ) {
    }
}
