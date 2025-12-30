<?php

namespace App\Application\Properties\Overview\DTOs;

use Carbon\CarbonImmutable;

final readonly class MsaMarketPulseDTO
{
    public function __construct(
        public string $msa,
        public ?string $msaName,
        public string $provider,
        public ?float $inventoryPressure,
        public ?float $supplyDemand,
        public ?float $pricingMomentum,
        /** @var array<string, mixed> */
        public array $payload,
        public CarbonImmutable $fetchedAt,
        public CarbonImmutable $expiresAt,
        public string $status = 'ready',
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'msa' => $this->msa,
            'msa_name' => $this->msaName,
            'provider' => $this->provider,
            'inventory_pressure' => $this->inventoryPressure,
            'supply_demand' => $this->supplyDemand,
            'pricing_momentum' => $this->pricingMomentum,
            'payload' => $this->payload,
            'fetched_at' => $this->fetchedAt->toIso8601String(),
            'expires_at' => $this->expiresAt->toIso8601String(),
            'status' => $this->status,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage,
        ];
    }

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            msa: (string) ($payload['msa'] ?? ''),
            msaName: $payload['msa_name'] ?? null,
            provider: (string) ($payload['provider'] ?? 'housecanary'),
            inventoryPressure: isset($payload['inventory_pressure']) ? (float) $payload['inventory_pressure'] : null,
            supplyDemand: isset($payload['supply_demand']) ? (float) $payload['supply_demand'] : null,
            pricingMomentum: isset($payload['pricing_momentum']) ? (float) $payload['pricing_momentum'] : null,
            payload: $payload['payload'] ?? [],
            fetchedAt: CarbonImmutable::parse($payload['fetched_at'] ?? 'now'),
            expiresAt: CarbonImmutable::parse($payload['expires_at'] ?? 'now'),
            status: (string) ($payload['status'] ?? 'ready'),
            errorCode: $payload['error_code'] ?? null,
            errorMessage: $payload['error_message'] ?? null,
        );
    }
}
