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
        public ?int $yearBuilt,
        public ?int $daysOnMarket,
        public ?float $distance,
        public string $address,
        public ?string $lastSeenDate,
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
            'squareFootage' => $this->squareFootage,
            'distance' => $this->distance,
            'daysOnMarket' => $this->daysOnMarket,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'lastSeenDate' => $this->lastSeenDate,
        ];
    }
}