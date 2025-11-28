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

        $raw = $this->marketDataProvider->fetchAll($property->address, $property->place_id,$coordinates);
        /**
         * ------------------------------------------
         * 1. Build PropertyDTO (subject property)
         * PropertyDTO is part a of property DB and Property value from provider (rentCast)
         * ------------------------------------------
         */
//        $subject = $raw->subjectProperty ?? [];

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
            type: $property->property_type,
            bedrooms: $property->bedrooms,
            bathrooms: $property->bathrooms,
            square_footage: $property->square_footage,
        );
        /**
         * ------------------------------------------
         * 2. Build FiltersDTO
         * ------------------------------------------
         */
        $filtersDto = FiltersDTO::defaults();
        /**
         * ------------------------------------------
         * 3. MARKET SNAPSHOT (avgPrice, avgRent, DOM, trend)
         * ------------------------------------------
         */

        $marketSnapshot =  MarketSnapshotDTO::fromArray(['sale' => $raw->saleComps,  'rent' => $raw->rentComps]);
        /**
         * ------------------------------------------
         * 4. VALUE ESTIMATE (sale + rent)
         * ------------------------------------------
         */
        $valueEstimateDto = new ValueEstimateDTO(
            $raw->valueEstimate['price'],
            $raw->valueEstimate['rangeLow'],
            $raw->valueEstimate['rangeHigh'],
        );
        /**
         * ------------------------------------------
         * 5. COMPARABLES
         * ------------------------------------------
         */
        $comparablesDto = ComparableDTO::fromArray($raw->saleComps, $raw->rentComps ?? []);
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

}