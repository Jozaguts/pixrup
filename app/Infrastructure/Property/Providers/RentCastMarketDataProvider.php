<?php

namespace App\Infrastructure\Property\Providers;

use App\Application\Properties\DTOs\SpyHuntRawResponseDTO;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Domain\Properties\ValueObjects\Coordinates;
use Illuminate\Http\Client\PendingRequest;
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

    public function fetchAll(string $address, Coordinates $coordinates): SpyHuntRawResponseDTO
    {
        $valueResponse = $this->client->get('avm/value',[
            'address' => $address,
            'compCount' => 10,
        ]);

        if (!$valueResponse->successful()) {
            throw new \RuntimeException('RentCast Value API failed: ' . $valueResponse->body());
        }

        $valueData = $valueResponse->json();

        $subject = $valueData['subjectProperty'] ?? [];

        $valueEstimate = [
            'price' => $valueData['price'],
            'rangeLow' => $valueData['priceRangeLow'] ?? null,
            'rangeHigh' => $valueData['priceRangeHigh'] ?? null,
        ];

        $salesComps = $valueData['comparables'] ?? [];

        $rentResponse = $this->client->get('avm/rent/long-term',[
            'address' => $address,
            'compCount' => 10,
        ]);
        $rentData = null;
        $rentEstimate = null;
        $rentComps = null;

        if ($rentResponse->successful()) {
            $rentData = $rentResponse->json();
            $rentEstimate = [
                'price' => $rentData['rent'] ?? null,
                'rangeLow' => $rentData['rentRangeLow'] ?? null,
                'rangeHigh' => $rentData['rentRangeHigh'] ?? null,
            ];
            $rentComps = $rentData['comparables'] ?? [];
        }

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