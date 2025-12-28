<?php

namespace App\Application\Properties\DTOs;

class PropertyDTO
{
public function __construct(
    public string $title,
    public string $status,
    public string $address,
    public ?string $city,
    public string $state,
    public ?string $postal_code,
    public string $country,
    public float $lat,
    public float $lng,
    public string $place_id,
    public mixed $metadata,
    public string $property_type,
    public int $bedrooms,
    public int $bathrooms,
    public int $square_footage,
){}
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'status' => $this->status,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'place_id' => $this->place_id,
            'metadata' => $this->metadata,
            'property_type' => $this->property_type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'square_footage' => $this->square_footage,
        ];
    }
    public static function fromArray(array $raw): self
    {
        return new self(
            $raw['title'],
            $raw['status'],
            $raw['address'],
            $raw['city'],
            $raw['state'],
            $raw['postal_code'],
            $raw['country'],
            $raw['lat'],
            $raw['lng'],
            $raw['place_id'],
            $raw['metadata'],
            $raw['property_type'] ?? $raw['type'],
            $raw['bedrooms'],
            $raw['bathrooms'],
            $raw['square_footage'],
        );
    }
}