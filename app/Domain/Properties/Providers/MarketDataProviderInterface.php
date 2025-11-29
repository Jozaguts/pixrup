<?php

namespace App\Domain\Properties\Providers;

use App\Domain\Properties\ValueObjects\Coordinates;

use App\Application\Properties\DTOs\{
    SubjectPropertyDTO,
    ValueEstimateDTO,
    MarketSnapshotDTO,
    ComparableDTO
};

interface MarketDataProviderInterface
{

    public function fetchSubjectProperty(string $address, Coordinates $coordinates): SubjectPropertyDTO;
    public function fetchValueEstimate(string $string, Coordinates $coordinates): ValueEstimateDTO;
    public function fetchMarketSnapshot(string $address, Coordinates $coordinates): MarketSnapshotDTO;
    public function fetchComprables(string $address, Coordinates $coordinates, array $filters = []): ComparableDTO;

}