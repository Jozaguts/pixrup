<?php

namespace App\Infrastructure\Property\Overview\Providers;

use App\Application\Properties\Overview\Contracts\OverviewProvider;
use App\Application\Properties\Overview\DTOs\PropertyOverviewSnapshotDTO;
use App\Application\Properties\Overview\DTOs\MsaMarketPulseDTO;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Infrastructure\Integrations\HouseCanary\HouseCanaryClient;
use App\Infrastructure\Integrations\HouseCanary\HouseCanaryParamsMapper;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;

final readonly class HouseCanaryOverviewProvider implements OverviewProvider
{
    private const string PROVIDER = 'housecanary';
    private const int SNAPSHOT_TTL_SECONDS = 86400;
    private const int MSA_PULSE_TTL_SECONDS = 604800;

    public function __construct(
        private HouseCanaryClient $client,
        private HouseCanaryParamsMapper $mapper,
    ) {}

    /**
     * @throws ConnectionException
     */
    public function fetchPropertySnapshot(PropertyEntity $property): PropertyOverviewSnapshotDTO
    {
        if ($property->id === null) {
            throw new \InvalidArgumentException('Property id is required for overview.');
        }

        $params = $this->mapper->fromProperty($property);

        $census = $this->extractResult($this->client->get('/v2/property/census', $params), 'property/census');
        $salesHistory = $this->extractResult($this->client->get('/v2/property/sales_history', $params), 'property/sales_history');
        $ownerOccupied = $this->extractResult($this->client->get('/v2/property/owner_occupied', $params), 'property/owner_occupied');
        $fema = $this->extractResult($this->client->get('/v2/property/fema_disaster_area', $params), 'property/fema_disaster_area');
        $flood = $this->extractResult($this->client->get('/v2/property/flood', $params), 'property/flood');
        $crime = $this->extractResult($this->client->get('/v2/property/block_crime', $params), 'property/block_crime');

        $ownerFlag = data_get($ownerOccupied, 'owner_occupied', data_get($ownerOccupied, 'ownerOccupied'));
        $femaFlag = data_get($fema, 'in_disaster_area');
        $crimeStats = data_get($crime, 'property', data_get($crime, 'all'));
        $crimePercentile = $this->intOrNull(data_get($crimeStats, 'county_percentile'));
        $crimeCompareScope = $crimePercentile !== null ? 'county' : null;
        if ($crimeCompareScope === null) {
            $crimePercentile = $this->intOrNull(data_get($crimeStats, 'nation_percentile'));
            $crimeCompareScope = $crimePercentile !== null ? 'national' : null;
        }

        $now = CarbonImmutable::now('UTC');

        return new PropertyOverviewSnapshotDTO(
            propertyId: $property->id,
            provider: self::PROVIDER,
            ownerOccupied: $this->normalizeBool($ownerFlag),
            femaDisasterArea: $this->normalizeBool($femaFlag),
            floodZone: data_get($flood, 'flood_zone', data_get($flood, 'zone')),
            floodRisk: data_get($flood, 'flood_risk'),
            crimePercentile: $crimePercentile,
            crimeCompareScope: $crimeCompareScope,
            msa: data_get($census, 'msa'),
            msaName: data_get($census, 'msa_name'),
            censusTract: data_get($census, 'census_tract', data_get($census, 'tract')),
            blockGroup: data_get($census, 'block_group', data_get($census, 'blockGroup')),
            payload: [
                'census' => $census,
                'sales_history' => $salesHistory,
                'owner_occupied' => $ownerOccupied,
                'hazards' => [
                    'fema_disaster_area' => $fema,
                    'flood' => $flood,
                ],
                'block_crime' => $crime,
                'meta' => [
                    'housecanary' => [
                        'request' => $params,
                    ],
                ],
            ],
            fetchedAt: $now,
            expiresAt: $now->addSeconds(self::SNAPSHOT_TTL_SECONDS),
        );
    }

    public function fetchMsaMarketPulse(string $msa): MsaMarketPulseDTO
    {
        $response = $this->client->get('/v3/msa/market_pulse/latest', ['msa' => $msa]);
        $data = $this->decode($response);
        $listingStats = data_get($data, 'listingStats', []);
        $geoInfo = data_get($data, 'geoInfo', []);

        $now = CarbonImmutable::now('UTC');

        return new MsaMarketPulseDTO(
            msa: $msa,
            msaName: data_get($geoInfo, 'msaName'),
            provider: self::PROVIDER,
            inventoryPressure: $this->floatOrNull(data_get($listingStats, 'monthsOfSupplyMedian')),
            supplyDemand: $this->floatOrNull(data_get($listingStats, 'listingsUnderContractPercent')),
            pricingMomentum: $this->floatOrNull(data_get($listingStats, 'priceChangePercentMedian')),
            payload: [
                'geo_info' => $geoInfo,
                'listing_stats' => $listingStats,
                'meta' => [
                    'housecanary' => [
                        'request' => ['msa' => $msa],
                    ],
                ],
            ],
            fetchedAt: $now,
            expiresAt: $now->addSeconds(self::MSA_PULSE_TTL_SECONDS),
        );
    }

    /** @return array<string, mixed> */
    private function extractResult(mixed $response, string $key): array
    {
        $data = $this->decode($response);
        $result = data_get($data, '0.' . $key . '.result');
        if ($result === null) {
            $result = data_get($data, $key . '.result');
        }

        if (is_array($result)) {
            return $result;
        }

        return is_array($data) ? $data : [];
    }

    /** @return array<string, mixed> */
    private function decode(mixed $response): array
    {
        if ($response instanceof Response) {
            return $response->json() ?? [];
        }

        return is_array($response) ? $response : [];
    }

    private function normalizeBool(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));
            if (in_array($normalized, ['y', 'yes', 'true', '1'], true)) {
                return true;
            }
            if (in_array($normalized, ['n', 'no', 'false', '0'], true)) {
                return false;
            }
        }

        return null;
    }

    private function floatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function intOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) round((float) $value) : null;
    }
}
