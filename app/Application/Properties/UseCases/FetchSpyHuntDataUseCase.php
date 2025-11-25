<?php

namespace App\Application\Properties\UseCases;

use App\Application\Properties\DTOs\ComparableDTO;
use App\Application\Properties\DTOs\FiltersDTO;
use App\Application\Properties\DTOs\MarketSnapshotDTO;
use App\Application\Properties\DTOs\PropertyDTO;
use App\Application\Properties\DTOs\SourceDTO;
use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Application\Properties\DTOs\StatsDTO;
use App\Application\Properties\DTOs\ValueEstimateDTO;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;
use Illuminate\Support\Carbon;

readonly class FetchSpyHuntDataUseCase
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private SpyHuntMarketDataProviderInterface $marketDataProvider,
        private SpyHuntCacheRepositoryInterface $cacheRepository,
    ) {}
    public function execute(int $propertyId, $filters = []): SpyHuntDataDTO
    {

        if ($cached = $this->cacheRepository->get($propertyId)) {
            return $cached;
        }
        $property = $this->propertyRepository->findOrFail($propertyId);
        $coordinates = $property->coordinates();
        $radius = $filters['radius'] ?? 1.0;

        $raw = $this->marketDataProvider->fetchAll($property->address, $coordinates);
        /**
         * ------------------------------------------
         * 1. Build PropertyDTO (subject property)
         * PropertyDTO is part a of property DB and Property value from provider (rentCast)
         * ------------------------------------------
         */
        $subject = $raw->subject ?? [];

        $propertyDto = new PropertyDTO(
            // form DB
            title: $property->title,
            status: $property->status,
            address: $property->address,
            city: $property->city,
            state: $property->state,
            postal_code: $property->postal_code,
            country: $property->country,
            lat: $property->lat,
            lng: $property->lng,
            place_id: $property->place_id,
            metadata: $property->metadata,
            // from provider
            type: $subject['propertyType'] ?? null,
            bedrooms: $subject['bedrooms'] ?? null,
            bathrooms: $subject['bathrooms'] ?? null,
            square_footage: $subject['squareFootage'] ?? null,
        );
        /**
         * ------------------------------------------
         * 2. Build FiltersDTO
         * ------------------------------------------
         */
        $filtersDto = new FiltersDTO(
            radiusOptions: [1,3,5],
            propertyTypes: ['Single Family','Condo','Townhouse','Manufactured','Multi-Family','Apartment','Land'],
            priceMin: 50000,
            priceMax: 3000000,
            defaultRadius: 3,
            defaultPropertyType: 'any',
            defaultMode: 'sale'
        );
        /**
         * ------------------------------------------
         * 3. MARKET SNAPSHOT (avgPrice, avgRent, DOM, trend)
         * ------------------------------------------
         */
        $averages = $this->getAvgValues(['rent' => $raw->rentComps,  'sale' => $raw->saleComps]);
        $marketSnapshot = new MarketSnapshotDTO(
            avgPricePerFt: $averages['avgPricePerFt'],
            avgRentPerFt: $averages['avgRentPerFt'],
            daysOnMarket: $averages['daysOnMarket'],
            trend30d: $averages['trend30d'],
        );
        /**
         * ------------------------------------------
         * 4. VALUE ESTIMATE (sale + rent)
         * ------------------------------------------
         */
        $valueEstimateDto = new ValueEstimateDTO(
            $raw->valueEstimate['price'],
            $raw->valueEstimate['priceRangeLow'],
            $raw->valueEstimate['priceRangeHigh'],
        );
        /**
         * ------------------------------------------
         * 5. COMPARABLES
         * ------------------------------------------
         */
        $saleComps = array_map(fn($c) => $this->mapComparableItem($c), $raw->saleComps);
        $rentComps = $raw->rentComps
            ? array_map(fn($c) => $this->mapComparableItem($c), $raw->rentComps)
            : [];
        $comparablesDto = new ComparableDTO(
            saleComps: $saleComps,
            rentComps: $rentComps,
        );
        /**
         * ------------------------------------------
         * 6. STATS
         * ------------------------------------------
         */
        $statsDto = $this->buildStats(
            saleComps: $raw->saleComps,
            filters: $filters
        );
        /**
         * ------------------------------------------
         * 7. SOURCE
         * ------------------------------------------
         */
        $sourceDto = new SourceDTO(
            source: 'avm',
            lastSync: now()->toISOString(),
        );

        $spyHuntDto = new SpyHuntDataDTO(
            property: $propertyDto,
            filters: $filtersDto,
            marketSnapshot: $marketSnapshot,
            valueEstimate: $valueEstimateDto,
            comparables: $comparablesDto,
            stats: $statsDto,
            source: $sourceDto
        );
        $this->cacheRepository->put($propertyId, $spyHuntDto, 86400);

        return $spyHuntDto;

    }
    /**
     * -------------------------------------------------
     * HELPER: Compare averages for price/ft, rent/ft, DOM, trend
     * -------------------------------------------------
     */
    private function getAvgValues(array $comparables): array
    {
        $now = now();

        $pricePerFt = [];
        $rentPricePerFt = [];
        $domValues = [];
        $recentComps = [];

        /** SALE COMPS */
        foreach ($comparables['sale'] ?? [] as $comp) {
            $price = $comp['price'] ?? null;
            $sqft  = $comp['squareFootage'] ?? null;
            $dom   = $comp['daysOnMarket'] ?? null;
            $lastSeen = isset($comp['lastSeen']) ? \Carbon\Carbon::parse($comp['lastSeen']) : null;

            if ($price && $sqft && $sqft > 50) {
                $pricePerFt[] = $price / $sqft;
            }
            if ($dom !== null && $dom > 0) {
                $domValues[] = $dom;
            }
            if ($lastSeen && $lastSeen->greaterThan($now->copy()->subDays(30))) {
                if ($price && $sqft && $sqft > 50) {
                    $recentComps[] = $price / $sqft;
                }
            }
        }

        /** RENT COMPS */
        foreach ($comparables['rent'] ?? [] as $comp) {
            $rent = $comp['price'] ?? null;
            $sqft = $comp['squareFootage'] ?? null;

            if ($rent && $sqft && $sqft > 50) {
                $rentPricePerFt[] = $rent / $sqft;
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

    /**
     * -------------------------------------------------
     * HELPER: Stats calculations
     * -------------------------------------------------
     */
    private function buildStats(array $saleComps, array $filters): StatsDTO
    {
        $radius = $filters['radius'] ?? 1.0;

        // Radius matches
        $radiusMatches = collect($saleComps)
            ->filter(fn($c) => isset($c['distance']) && $c['distance'] <= $radius)
            ->count();

        // zip DOM
        $zipDom = collect($saleComps)
            ->pluck('daysOnMarket')
            ->filter(fn($d) => $d > 0)
            ->avg();

        // subject DOM
        $closest = collect($saleComps)->sortBy('distance')->first();
        $subjectDom = $closest['daysOnMarket'] ?? null;

        return new StatsDTO(
            radiusMatches: $radiusMatches,
            zipDom: $zipDom,
            buyerDemandScore: null,
            buyerDemandChange: null,
            subjectDom: $subjectDom,
        );
    }

    /**
     * -------------------------------------------------
     * HELPER: Map comparable item
     * -------------------------------------------------
     */
    private function mapComparableItem(array $comp): array
    {
        return [
            'price' => $comp['price'] ?? null,
            'sqft' => $comp['squareFootage'] ?? null,
            'bedrooms' => $comp['bedrooms'] ?? null,
            'bathrooms' => $comp['bathrooms'] ?? null,
            'year_built' => $comp['yearBuilt'] ?? null,
            'dom' => $comp['daysOnMarket'] ?? null,
            'distance' => $comp['distance'] ?? null,
            'address' => $comp['address'] ?? null,
            'last_seen' => $comp['lastSeen'] ?? null,
        ];
    }

}