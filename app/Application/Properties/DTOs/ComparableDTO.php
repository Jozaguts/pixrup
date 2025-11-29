<?php

namespace App\Application\Properties\DTOs;

class ComparableDTO
{
    /**
     * @param array $saleComps   Lista de comparables de venta
     * @param array|null $rentComps Lista de comparables de renta
     */
    public function __construct(
        public array $saleComps,
        public ?array $rentComps = null,
    ){}
    public function toArray(): array
    {
        return [
           'summary' => [
               'sale_count' => count($this->saleComps),
               'rent_count' => $this->rentComps ? count($this->rentComps) : 0,
           ],
            'sale' => $this->saleComps,
            'rent' => $this->rentComps,
        ];
    }
    public static function fromArray(array $salesComps, array $rentComps): ComparableDTO
    {
        return new ComparableDTO(
            array_map(fn($c) => self::mapComparableItem($c), $salesComps),
            count($rentComps)
                ? array_map(fn($c) => self::mapComparableItem($c), $rentComps)
                : []
            );
    }

    /**
     * -------------------------------------------------
     * HELPER: Map comparable item
     * -------------------------------------------------
     */
    private static function mapComparableItem(array $comp): array
    {
        return [
            'price' => $comp['price'] ,
            'squareFootage' => $comp['squareFootage'] ?? null,
            'bedrooms' => $comp['bedrooms'] ?? null ,
            'bathrooms' => $comp['bathrooms'] ?? null,
            'yearBuilt' => $comp['yearBuilt'] ?? null,
            'daysOnMarket' => $comp['daysOnMarket'],
            'distance' => $comp['distance'],
            'address' => $comp['address'] ?? $comp['formattedAddress'],
            'lastSeenDate' => $comp['lastSeenDate'],
            'propertyType' => $comp['propertyType'],
            'latitude' => $comp['latitude'],
            'longitude' => $comp['longitude'],
        ];
    }
}