<?php

namespace App\Application\Properties\DTOs;

use Carbon\Carbon;

class MarketSnapshotDTO
{
    public function __construct(
        public float $avgPricePerFt,
        public ?float $avgRentPerFt,
        public float $avgSalePrice,
        public ?float $avgRentPrice,
        public int $daysOnMarket,
        public mixed $trend30d,
        public mixed $trend30dTotal,
    ) {
    }
    public function toArray(): array
    {
        return [
            'avgPricePerFt' => $this->avgPricePerFt,
            'avgRentPerFt' => $this->avgRentPerFt,
            'avgSalePrice' => $this->avgSalePrice,
            'avgRentPrice' => $this->avgRentPrice,
            'daysOnMarket' => $this->daysOnMarket,
            'trend30d' => $this->trend30d,
            'trend30dTotal' => $this->trend30dTotal,
        ];
    }
    public static function fromArray(array $comparables): MarketSnapshotDTO
    {
        $averages = self::getAvgValues($comparables);

        return new MarketSnapshotDTO(
            avgPricePerFt: $averages['avgPricePerFt'] ??  null,
            avgRentPerFt: $averages['avgRentPerFt'] ?? null,
            avgSalePrice: $averages['avgSalePrice'] ??  null,
            avgRentPrice: $averages['avgRentPrice'] ?? null,
            daysOnMarket: $averages['daysOnMarket'] ?? null,
            trend30d: $averages['trend30d'] ?? null,
            trend30dTotal: $averages['trend30dTotal'] ?? null,
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
        $salePrices = [];
        $rentPrices = [];
        $salePricePerFt = [];
        $rentPricePerFt = [];
        $domValues = [];
        $recentSalePerFt = [];
        $recentSalePrices = [];

        /** SALE COMPS */
        foreach ($comparables['sale'] ?? [] as $comp) {
            $price = $comp['price'] ?? null;
            $sf = $comp['squareFootage'] ?? null;
            $dom = $comp['daysOnMarket'] ?? null;
            $lastSeenRaw = $comp['lastSeenDate'] ?? null;
            $lastSeen = $lastSeenRaw ? Carbon::parse($lastSeenRaw) : null;

            /** NUEVO: promedio de precio total */
            if ($price) {
                $salePrices[] = $price;
            }

            /** EXISTENTE: price per ft² */
            if ($price && $sf && $sf > 50) {
                $salePricePerFt[] = $price / $sf;
            }

            /** EXISTENTE: DOM */
            if ($dom !== null && $dom > 0) {
                $domValues[] = $dom;
            }

            /** EXISTENTE: recent comps pero ahora guardamos AMBOS */
            if ($lastSeen && $lastSeen->greaterThan($now->copy()->subDays(30))) {

                if ($price) {
                    $recentSalePrices[] = $price;
                }

                if ($price && $sf && $sf > 50) {
                    $recentSalePerFt[] = $price / $sf;
                }
            }
        }

        /** RENT COMPS */
        foreach ($comparables['rent'] ?? [] as $comp) {
            $rent = $comp['price'] ?? null;
            $sf = $comp['squareFootage'] ?? null;

            if ($rent) {
                $rentPrices[] = $rent;
            }

            /** EXISTENTE: rent per ft² */
            if ($rent && $sf && $sf > 50) {
                $rentPricePerFt[] = $rent / $sf;
            }
        }

        $avgSalePrice = count($salePrices) ? array_sum($salePrices) / count($salePrices) : null;
        $avgRentPrice = count($rentPrices) ? array_sum($rentPrices) / count($rentPrices) : null;

        $avgSalePricePerFt = count($salePricePerFt) ? array_sum($salePricePerFt) / count($salePricePerFt) : null;
        $avgRentPricePerFt = count($rentPricePerFt) ? array_sum($rentPricePerFt) / count($rentPricePerFt) : null;

        $daysOnMarket = count($domValues) ? array_sum($domValues) / count($domValues) : null;

        $recentAvgPerFt = count($recentSalePerFt)
            ? array_sum($recentSalePerFt) / count($recentSalePerFt)
            : null;

        $recentAvgPrice = count($recentSalePrices)
            ? array_sum($recentSalePrices) / count($recentSalePrices)
            : null;

        $trend30dPerFt = ($recentAvgPerFt && $avgSalePricePerFt)
            ? (($recentAvgPerFt - $avgSalePricePerFt) / $avgSalePricePerFt) * 100
            : null;
        $trend30dTotal = ($recentAvgPrice && $avgSalePrice)
            ? (($recentAvgPrice - $avgSalePrice) / $avgSalePrice) * 100
            : null;
        return [
            'avgPricePerFt'     => $avgSalePricePerFt,
            'avgRentPerFt'      => $avgRentPricePerFt,
            'avgSalePrice' => $avgSalePrice,
            'avgRentPrice' => $avgRentPrice,
            'daysOnMarket' => $daysOnMarket,
            'trend30d' => $trend30dPerFt,
            'trend30dTotal'     => $trend30dTotal,
        ];
    }

}