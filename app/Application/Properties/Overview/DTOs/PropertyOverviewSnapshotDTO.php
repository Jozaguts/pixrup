<?php

namespace App\Application\Properties\Overview\DTOs;

use Carbon\CarbonImmutable;

final readonly class PropertyOverviewSnapshotDTO
{
    public function __construct(
        public int $propertyId,
        public string $provider,
        public ?bool $ownerOccupied,
        public ?bool $femaDisasterArea,
        public ?string $floodZone,
        public ?string $floodRisk,
        public ?int $crimePercentile,
        public ?string $crimeCompareScope,
        public ?string $msa,
        public ?string $msaName,
        public ?string $censusTract,
        public ?string $blockGroup,
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
            'property_id' => $this->propertyId,
            'provider' => $this->provider,
            'owner_occupied' => $this->ownerOccupied,
            'fema_disaster_area' => $this->femaDisasterArea,
            'flood_zone' => $this->floodZone,
            'flood_risk' => $this->floodRisk,
            'crime_percentile' => $this->crimePercentile,
            'crime_compare_scope' => $this->crimeCompareScope,
            'msa' => $this->msa,
            'msa_name' => $this->msaName,
            'census_tract' => $this->censusTract,
            'block_group' => $this->blockGroup,
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
            propertyId: (int) ($payload['property_id'] ?? 0),
            provider: (string) ($payload['provider'] ?? 'housecanary'),
            ownerOccupied: $payload['owner_occupied'] ?? null,
            femaDisasterArea: $payload['fema_disaster_area'] ?? null,
            floodZone: $payload['flood_zone'] ?? null,
            floodRisk: $payload['flood_risk'] ?? null,
            crimePercentile: isset($payload['crime_percentile']) ? (int) $payload['crime_percentile'] : null,
            crimeCompareScope: $payload['crime_compare_scope'] ?? null,
            msa: $payload['msa'] ?? null,
            msaName: $payload['msa_name'] ?? null,
            censusTract: $payload['census_tract'] ?? null,
            blockGroup: $payload['block_group'] ?? null,
            payload: $payload['payload'] ?? [],
            fetchedAt: CarbonImmutable::parse($payload['fetched_at'] ?? 'now'),
            expiresAt: CarbonImmutable::parse($payload['expires_at'] ?? 'now'),
            status: (string) ($payload['status'] ?? 'ready'),
            errorCode: $payload['error_code'] ?? null,
            errorMessage: $payload['error_message'] ?? null,
        );
    }
}
