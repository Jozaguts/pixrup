<?php

namespace App\Infrastructure\Property\Providers;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Models\Property;
use GuzzleHttp\Promise\PromiseInterface;
use http\Exception\RuntimeException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HouseCanaryProvider implements AppraisalProviderInterface
{
    private PendingRequest $client;
    public function __construct() {
        $this->client = Http::withBasicAuth(
            config('services.house_canary.api_key'),
            config('services.house_canary.secret')
        )
            ->baseUrl(config('services.house_canary.base_url'))
            ->acceptJson();
    }

    public function fetchValue(PropertyEntity $property): PropertyWorthDTO
    {
        $params = $this->buildHCParams($property);

        $valueResp = $this->get('/v2/property/value', [
            'address' => $params['address'],
            'city'    => $params['city'],
            'state'   => $params['state'],
            'zipcode' => $params['zipcode'],
        ])->json();

        $value = $valueResp[0]['property/value']['result']['value'];
        $mean = $value['price_mean'];
        $low  = $value['price_lwr'];
        $high = $value['price_upr'];
        $fsd  = $value['fsd'];

        // COMPS
        $compsResp = $this->get('/v3/property/comps_sale', [
            'address' => $params['address'],
            'city'    => $params['city'],
            'state'   => $params['state'],
            'zipcode' => $params['zipcode'],
            'num_comps' => 5
        ])->json();
        $comps = $compsResp['sales_comps'];

        $historyResp = $this->get('/v3/property/historical_value', [
            'address' => $params['address'],
            'city'    => $params['city'],
            'state'   => $params['state'],
            'zipcode' => $params['zipcode'],
            'date' => now()->subDays(30)->format('Y-m-d'),
        ])->json();

        $pastMean = $historyResp['historical_value']['price_mean'] ?? null;

        $trend30 = $pastMean
            ? (($mean - $pastMean) / $pastMean) * 100
            : null;

        return new PropertyWorthDTO(
            value: $mean,
            value_low: $low,
            value_high: $high,
            confidence: 1 - $fsd,
            comparables: $comps,
            provider: 'housecanary',
            fetched_at: now(),
            cached_at: null,
            trend30: $trend30
        );
    }
    private function get(string $endpoint, $params = []): PromiseInterface|Response
    {
        $response = $this->client->get($endpoint, $params);

        if ($response->failed()) {
            throw new \RuntimeException(
                "HouseCanary error {$response->status()}: " . $response->body()
            );
        }
        return $response;
    }
    private function buildHCParams(PropertyEntity $property): array
    {
        return [
            'address' => $this->extractStreet($property),
            'city' => $property->city,
            'state' => $property->state,
            'zipcode' => $property->postal_code,
        ];
    }
    private function extractStreet(PropertyEntity $property): string
    {
        $full = $property->address;
        $suffix = ", {$property->city}, {$property->state} {$property->postal_code}";


        if (str_ends_with($full, $suffix)) {
            return substr($full, 0, -strlen($suffix));
        }


        return $full;
    }
}