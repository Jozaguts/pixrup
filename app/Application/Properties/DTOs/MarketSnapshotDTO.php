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
            'avgPricePerFt' => $this->avgPricePerFt,
            'avgRentPerFt' => $this->avgRentPerFt,
            'daysOnMarket' => $this->daysOnMarket,
            'trend30d' => $this->trend30d,
        ];
    }

}