<?php

namespace App\Infrastructure\Integrations\HouseCanary;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class HouseCanaryClient
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

    /**
     * @throws ConnectionException
     */
    public function get(string $endpoint, $params = []): PromiseInterface|Response
    {
        $response = $this->client->get($endpoint, $params);

        if ($response->failed()) {
            throw new \RuntimeException(
                "HouseCanary error {$response->status()}: " . $response->body()
            );
        }
        return $response;
    }
}