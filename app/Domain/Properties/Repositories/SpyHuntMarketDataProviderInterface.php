<?php

namespace App\Domain\Properties\Repositories;

use App\Application\Properties\DTOs\ComparablesDTO;
use App\Application\Properties\DTOs\MarketSnapshotDTO;
use App\Application\Properties\DTOs\SubjectPropertyDTO;
use App\Application\Properties\DTOs\ValueEstimateDTO;
use App\Domain\Properties\ValueObjects\Coordinates;

interface SpyHuntMarketDataProviderInterface
{
    public function fetchValueEstimate(string $address, Coordinates $coordinates): ValueEstimateDTO;

    public function fetchMarketSnapshot(string $address, Coordinates $coordinates): MarketSnapshotDTO;

    public function fetchComparables(string $address, Coordinates $coordinates, array  $filters = []): ComparablesDTO;

    public function fetchSubjectProperty(string $address, Coordinates $coordinates): SubjectPropertyDTO;

}