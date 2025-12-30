<?php

namespace App\Application\Properties\Overview\Contracts;

use App\Application\Properties\Overview\DTOs\PropertyOverviewSnapshotDTO;

interface OverviewRepository
{
    public function get(int $propertyId): ?PropertyOverviewSnapshotDTO;
    public function save(PropertyOverviewSnapshotDTO $dto): void;
    public function forget(int $propertyId): void;
}
