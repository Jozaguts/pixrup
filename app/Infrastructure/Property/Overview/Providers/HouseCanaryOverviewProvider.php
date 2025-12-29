<?php

namespace App\Infrastructure\Property\Overview\Providers;


use App\Application\Properties\Overview\Contracts\OverviewProvider;
use App\Application\Properties\Overview\DTOs\OverviewDTO;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Infrastructure\Integrations\HouseCanary\HouseCanaryClient;
use App\Infrastructure\Integrations\HouseCanary\HouseCanaryParamsMapper;

final readonly class HouseCanaryOverviewProvider implements OverviewProvider
{
    public function __construct( private HouseCanaryClient $client, private HouseCanaryParamsMapper $mapper,){}

    public function fetchOverview(PropertyEntity $property): OverviewDTO|array
    {
        $params = $this->mapper->fromProperty($property);
    }
}