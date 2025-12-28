<?php

namespace App\Domain\Properties\ValueObjects;

readonly class Coordinates
{
    public function __construct(public float $latitude, public float $longitude)
    {
        if($this->latitude < -90 || $this->latitude > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90 degrees.');
        }
        if($this->longitude < -180 || $this->longitude > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180 degrees.');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self($data['latitude'], $data['longitude']);
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }


}