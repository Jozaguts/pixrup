<?php

namespace App\Application\Properties\DTOs;

/*
 * @var $tpe string 'sold'|'active'|'rent'
 * */
class ComparableItemDTO
{
    public function __construct(
        public string $id,
        public ?float $price,
        public ?int $square_footage,
        public ?int $bedrooms,
        public ?int $bathrooms,
        public ?int $year_built,
        public ?int $dom,
        public ?float $distance,
        public string $address,
        public ?string $last_seen,
        public ?string $status,
        public float $latitude,
        public float $longitude,
    ){}
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'sqft' => $this->square_footage,
            'distance' => $this->distance,
            'dom' => $this->dom,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'last_seen' => $this->last_seen,
        ];
    }
}