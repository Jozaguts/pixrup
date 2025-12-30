<?php

namespace App\Application\Properties\Overview\Contracts;

use App\Application\Properties\Overview\DTOs\PropertyOverviewSnapshotDTO;
use App\Application\Properties\Overview\DTOs\MsaMarketPulseDTO;
use App\Domain\Properties\Entities\PropertyEntity;

interface OverviewProvider
{
    public function fetchPropertySnapshot(PropertyEntity $property): PropertyOverviewSnapshotDTO;
    public function fetchMsaMarketPulse(string $msa): MsaMarketPulseDTO;
}
