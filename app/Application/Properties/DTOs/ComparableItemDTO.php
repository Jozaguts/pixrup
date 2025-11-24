<?php

namespace App\Application\Properties\DTOs;

/*
 * @var $tpe string 'sold'|'active'|'rent'
 * */
class ComparableItemDTO
{
    public function __construct(
        public int $id,
        public string $address,
        public string $type,
        public int $bedrooms,
        public int $bathrooms,
        public int $square_footage,
        public float $distance,
        public int $days_on_market,
        public string $photo,
        public float $latitude,
        public float $longitude,
    ){}
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'type' => $this->type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'square_footage' => $this->square_footage,
            'distance' => $this->distance,
            'days_on_market' => $this->days_on_market,
            'photo' => $this->photo,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}