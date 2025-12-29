<?php

namespace App\Infrastructure\Property\PixrWorth\Providers;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\Contracts\WorthProvider;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Infrastructure\Integrations\HouseCanary\HouseCanaryClient;
use App\Infrastructure\Integrations\HouseCanary\HouseCanaryParamsMapper;
use Illuminate\Http\Client\ConnectionException;

final readonly class HouseCanaryWorthProvider implements WorthProvider
{
    public function __construct(
        private HouseCanaryClient $client,
        private HouseCanaryParamsMapper $mapper,
    ) {}

    /**
     * @throws ConnectionException
     */
    public function appraisal(PropertyEntity $property): PropertyWorthDTO
    {
        $params = $this->mapper->fromProperty($property);

        $valueResp = $this->client->get('/v2/property/value', $params);
        $value = $valueResp[0]['property/value']['result']['value'] ?? [];

        $mean = $value['price_mean'] ?? null;
        $low  = $value['price_lwr'] ?? null;
        $high = $value['price_upr'] ?? null;
        $fsd  = $value['fsd'] ?? null;

        $compsResp = $this->client->get('/v3/property/comps_sale', $params + ['num_comps' => 5]);
        $comps = $compsResp['sales_comps'] ?? [];

        $historyResp = $this->client->get('/v3/property/historical_value', $params + [
                'date' => now()->subDays(30)->format('Y-m-d'),
            ]);
        $pastMean = $historyResp['historical_value']['price_mean'] ?? null;

        $trend30 = ($pastMean && $mean)
            ? (($mean - $pastMean) / $pastMean) * 100
            : null;

        return new PropertyWorthDTO(
            value: $mean,
            value_low: $low,
            value_high: $high,
            confidence: ($fsd !== null) ? 1 - $fsd : null,
            comparables: $comps,
            provider: 'housecanary',
            fetched_at: now(),
            cached_at: null,
            trend30: $trend30
        );
    }
}