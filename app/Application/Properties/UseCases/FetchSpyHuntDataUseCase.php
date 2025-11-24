<?php

namespace App\Application\Properties\UseCases;

use App\Application\Properties\DTOs\FiltersDTO;
use App\Application\Properties\DTOs\PropertyDTO;
use App\Application\Properties\DTOs\SourceDTO;
use App\Application\Properties\DTOs\SpyHuntDataDTO;
use App\Application\Properties\DTOs\StatsDTO;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;

readonly class FetchSpyHuntDataUseCase
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private SpyHuntMarketDataProviderInterface $marketDataProvider,
        private SpyHuntCacheRepositoryInterface $cacheRepository,
    ) {}
    public function execute(int $propertyId, $filters = []): SpyHuntDataDTO
    {
        $property = $this->propertyRepository->findOrFail($propertyId);

        $coordinates = $property->coordinates();
        $cached = $this->cacheRepository->get($propertyId);
        if ($cached !== null) {
            return $cached;
        }
        $valueEstimate = $this->marketDataProvider->fetchValueEstimate($property->address, $coordinates);
        $marketSnapshot = $this->marketDataProvider->fetchMarketSnapshot($property->address, $coordinates);
        $comparables = $this->marketDataProvider->fetchComparables($property->address, $coordinates, $filters);
        $subject = $this->marketDataProvider->fetchSubjectProperty($property->address, $coordinates);

        $propertyDto = new PropertyDTO(
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
            type: $subject->type,
            bedrooms: $subject->bedrooms,
            bathrooms: $subject->bathrooms,
            square_footage: $subject->squareFootage
        );
        $filtersDto = new FiltersDTO(
            radiusOptions: [1,3,5],
            propertyTypes: ['Single Family','Condo','Multi-Family','Townhouse','Apartment','Manufactured','Vacant'],
            priceMin: 50000,
            priceMax: 3000000,
            defaultRadius: 3,
            defaultPropertyType: 'any',
            defaultMode: 'sale'
        );
        $domValues = array_map(fn ($comp) => $comp->daysOnMarket, $comparables->items->toArray());
        $zipDom = count($domValues) ? array_sum($domValues) / count($domValues) : null;
        $statsDto = new StatsDTO(
            radiusMatches: count($comparables->items->toArray()),
            zipDom: $zipDom,
            buyerDemandScore: null,
            buyerDemandChange: null,
            subjectDom: null
        );
        $sourceDto = new SourceDTO(
            source: 'avm',
            lastSync: now()->toIso8601String(),
        );
        $spyHuntDto = new SpyHuntDataDTO(
            property: $propertyDto,
            filters: $filtersDto,
            marketSnapshot: $marketSnapshot,
            valueEstimate: $valueEstimate,
            comparables: $comparables,
            stats: $statsDto,
            source: $sourceDto
        );
        $this->cacheRepository->put($propertyId, $spyHuntDto, 86400);

        return $spyHuntDto;

    }

}