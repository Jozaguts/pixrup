<?php

namespace App\Application\Properties\Overview\UseCases;

use App\Application\Properties\Overview\Contracts\OverviewCache;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Properties\Entities\PropertyEntity;

class CreatePropertyOverviewUseCase
{
    public function __construct(
        private OverviewCache $cache,
        private AppraisalProviderInterface $provider,
    ) {}

    public function execute(PropertyEntity $propertyEntity)
    {
        return  [];
    }
}