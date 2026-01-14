<?php

/**
 * Description: File defining the AppraisePropertyWorthUseCase orchestrating PixrWorth retrieval.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Exposes a use case to drive backend controller interactions.
 */

namespace App\Application\Properties\PixrWorth\UseCases;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\Contracts\WorthProvider;
use App\Application\Properties\PixrWorth\Contracts\WorthRepository;
use App\Application\Usage\Contracts\UsageGuard;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Usage\Enums\UsageAction;

/**
 * Description: Use case coordinating property worth fetching via the application service.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Controllers invoke this use case to obtain valuation DTOs for responses.
 */

readonly class AppraisePropertyWorthUseCase
{
    private const int CACHE_TTL_HOURS = 24;
        public function __construct(
        private WorthRepository $repo,
        private WorthProvider $provider,
        private UsageGuard $usage,
    ) {}

    /**
     * Description: Execute the use case returning valuation data for the supplied property.
     * Parameters: Property $property Property instance to evaluate.
     * Returns: PropertyWorthDTO
     * Expected Result: Returns DTO produced by the application service for the property.
     */
    public function execute(PropertyEntity $property, bool $force = false): PropertyWorthDTO
    {
        $threshold = now()->subHours(self::CACHE_TTL_HOURS);

        if (!$force && $cached = $this->repo->findFresh($property->id, $threshold)) {
            return $cached;
        }
        $this->usage->ensure(UsageAction::APPRAISAL, $property);

        $dto = $this->provider->appraisal($property);

        $this->repo->save($property->id, $dto);

        return $dto;
    }
}
