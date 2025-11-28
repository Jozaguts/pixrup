<?php

namespace App\Infrastructure\Property\Providers;

use App\Application\Properties\DTOs\SpyHuntRawResponseDTO;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Domain\Properties\ValueObjects\Coordinates;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RentCastMarketDataProvider implements SpyHuntMarketDataProviderInterface
{
    private PendingRequest $client;
    public function __construct(
        private readonly string $apiKey,
    ) {
        $this->client = Http::withHeaders(
            ['X-Api-Key' => $this->apiKey,
                'accept'    => 'application/json'
            ],
        )
            ->baseUrl(config('services.rentcast.base_url'));
    }

    public function fetchAll(string $address, string $place_id, Coordinates $coordinates): SpyHuntRawResponseDTO
    {

        $valueData = Cache::remember('avm:'.$place_id, 86400, function() use($address) {
            $response = $this->client->get('avm/value',[
                'address' => $address,
                'compCount' => 10,
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException('RentCast Value API failed: ' . $response->body());
            }
            return  $response->json();
        });


        $subject = $valueData['subjectProperty'] ?? [];

        $valueEstimate = [
            'price' => $valueData['price'],
            'rangeLow' => $valueData['priceRangeLow'] ?? null,
            'rangeHigh' => $valueData['priceRangeHigh'] ?? null,
        ];

        $salesComps = $valueData['comparables'] ?? [];

        $rentData = Cache::remember('long-term:'.$place_id, 86400, function() use($address) {
            $response = $this->client->get('avm/rent/long-term',[
                'address' => $address,
                'compCount' => 10,
            ]);
            if (!$response->successful()) {
                throw new \RuntimeException('RentCast Value API failed: ' . $response->body());
            }
            return  $response->json();
        });

        $rentEstimate = [
            'price' => $rentData['rent'] ?? null,
            'rangeLow' => $rentData['rentRangeLow'] ?? null,
            'rangeHigh' => $rentData['rentRangeHigh'] ?? null,
        ];
        $rentComps = $rentData['comparables'] ?? [];

        return new SpyHuntRawResponseDTO(
            $subject,
            $valueEstimate,
            $salesComps,
            $rentEstimate,
            $rentComps,
            $valueData,
            $rentData
        );
    }
}