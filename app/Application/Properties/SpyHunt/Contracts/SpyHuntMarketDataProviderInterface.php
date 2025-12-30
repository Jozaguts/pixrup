<?php

namespace App\Application\Properties\SpyHunt\Contracts;

use App\Application\Properties\DTOs\SpyHuntRawResponseDTO;
use App\Domain\Properties\ValueObjects\Coordinates;

interface SpyHuntMarketDataProviderInterface
{
    public function fetchAll(string $address, string $place_id, Coordinates $coordinates): SpyHuntRawResponseDTO;

}
