<?php

namespace App\Application\Properties\Overview\Contracts;

use App\Application\Properties\Overview\DTOs\OverviewDTO;
use App\Domain\Properties\Entities\PropertyEntity;

interface OverviewProvider
{
    public function fetchOverview(PropertyEntity $property): OverviewDTO|array;
}