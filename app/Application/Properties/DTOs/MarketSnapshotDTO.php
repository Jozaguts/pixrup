<?php

namespace App\Application\Properties\DTOs;

use Carbon\Carbon;

class MarketSnapshotDTO
{
    public function __construct(
        public float $avgPricePerFt,
        public ?float $avgRentPerFt,
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
    public static function fromArray(array $comparables): MarketSnapshotDTO
    {
        $averages = self::getAvgValues($comparables);

        return new MarketSnapshotDTO(
            avgPricePerFt: $averages['avgPricePerFt'] ??  null,
            avgRentPerFt: $averages['avgRentPerFt'] ?? null,
            daysOnMarket: $averages['daysOnMarket'] ?? null,
            trend30d: $averages['trend30d'] ?? null,
        );
    }
    /**
     * -------------------------------------------------
     * HELPER: Compare averages for price/ft, rent/ft, DOM, trend
     * -------------------------------------------------
     */
    private static function getAvgValues(array $comparables): array
    {
        $now = now();

        $pricePerFt = [];
        $rentPricePerFt = [];
        $domValues = [];
        $recentComps = [];

        /** SALE COMPS */
        foreach ($comparables['sale'] ?? [] as $comp) {
            $price = $comp['price'] ?? null;
            $squareFootage  = $comp['squareFootage'];
            $dom   = $comp['daysOnMarket'];
            $lastSeenRaw = $comp['lastSeenDate'];
            $lastSeen = $lastSeenRaw ? Carbon::parse($lastSeenRaw) : null;

            if ($price && $squareFootage && $squareFootage > 50) {
                $pricePerFt[] = $price / $squareFootage;
            }
            if ($dom !== null && $dom > 0) {
                $domValues[] = $dom;
            }
            if ($lastSeen && $lastSeen->greaterThan($now->copy()->subDays(30))) {
                if ($price && $squareFootage && $squareFootage > 50) {
                    $recentComps[] = $price / $squareFootage;
                }
            }
        }

        /** RENT COMPS */
        foreach ($comparables['rent'] ?? [] as $comp) {
            $rent =  $comp['price'] ?? null;
            $squareFootage = $comp['squareFootage'];

            if ($rent && $squareFootage && $squareFootage > 50) {
                $rentPricePerFt[] = $rent / $squareFootage;
            }
        }

        $avgPrice = count($pricePerFt) ? array_sum($pricePerFt) / count($pricePerFt) : null;
        $avgRent = count($rentPricePerFt) ? array_sum($rentPricePerFt) / count($rentPricePerFt) : null;

        $daysOnMarket = count($domValues) ? array_sum($domValues) / count($domValues) : null;

        $recentAvg = count($recentComps) ? array_sum($recentComps) / count($recentComps) : null;

        $trend30d = ($recentAvg && $avgPrice)
            ? (($recentAvg - $avgPrice) / $avgPrice) * 100
            : null;

        return [
            'avgPricePerFt' => $avgPrice,
            'avgRentPerFt' => $avgRent,
            'daysOnMarket' => $daysOnMarket,
            'trend30d' => $trend30d,
        ];
    }

}