<?php

namespace App\Application\Properties\Overview\UseCases;

use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Domain\Properties\Entities\PropertyEntity;

class CreatePropertyOverviewUseCase
{
    public function __construct(
        private OverviewRepository $cache,
    ) {}

    public function execute(PropertyEntity $propertyEntity)
    {
        return  [];
    }
}
