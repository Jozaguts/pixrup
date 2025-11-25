<?php

namespace App\Application\Properties\DTOs;

class MarketSnapshotDTO
{
    public function __construct(
        public float $avgPricePerFt,
        public float $avgRentPerFt,
        public int $daysOnMarket,
        public mixed $trend30d,
    ) {
    }
    public function toArray(): array
    {
        return [
            'avgPricePerFt' => $this->avgPricePerFt,  //sale value
            'avgRentPerFt' => $this->avgRentPerFt, // rent value
            'daysOnMarket' => $this->daysOnMarket, // sale value
            'trend30d' => $this->trend30d, //sale value
        ];
    }

}