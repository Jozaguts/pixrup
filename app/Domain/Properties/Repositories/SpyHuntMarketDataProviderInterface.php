<?php

namespace App\Domain\Properties\Repositories;

use App\Application\Properties\DTOs\SpyHuntRawResponseDTO;
use App\Domain\Properties\ValueObjects\Coordinates;

interface SpyHuntMarketDataProviderInterface
{
    public function fetchAll(string $address, Coordinates $coordinates): SpyHuntRawResponseDTO;

}