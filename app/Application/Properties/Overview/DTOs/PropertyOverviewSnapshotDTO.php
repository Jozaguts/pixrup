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
        public ?int $hoaAnnualEstimate,
        public ?int $hoaMinFee,
        public ?int $hoaMaxFee,
        public ?int $hoaSamples,
        public ?string $hoaSubdivision,
        public ?string $hoaSubdivisionId,
        public ?string $schoolDistrict,
        public ?string $elementarySchool,
        public ?string $middleSchool,
        public ?string $highSchool,
        public ?bool $hasPool,
        public ?bool $hasAttic,
        public ?string $basementType,
        public ?bool $hasAirConditioning,
        public ?string $fireplaceType,
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
            'hoa_annual_est' => $this->hoaAnnualEstimate,
            'hoa_min_fee' => $this->hoaMinFee,
            'hoa_max_fee' => $this->hoaMaxFee,
            'hoa_samples' => $this->hoaSamples,
            'hoa_subdivision' => $this->hoaSubdivision,
            'hoa_subdivision_id' => $this->hoaSubdivisionId,
            'school_district' => $this->schoolDistrict,
            'elementary_school' => $this->elementarySchool,
            'middle_school' => $this->middleSchool,
            'high_school' => $this->highSchool,
            'has_pool' => $this->hasPool,
            'has_attic' => $this->hasAttic,
            'basement_type' => $this->basementType,
            'has_air_conditioning' => $this->hasAirConditioning,
            'fireplace_type' => $this->fireplaceType,
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
        $hoaPayload = data_get($payload, 'payload.hoa_est.association_estimated', []);
        $schoolPayload = data_get($payload, 'payload.school.result.school', []);
        $detailsProperty = data_get($payload, 'payload.details.property', []);

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
            hoaAnnualEstimate: isset($payload['hoa_annual_est'])
                ? (int) $payload['hoa_annual_est']
                : (isset($hoaPayload['annual_hoa_est']) ? (int) $hoaPayload['annual_hoa_est'] : null),
            hoaMinFee: isset($payload['hoa_min_fee'])
                ? (int) $payload['hoa_min_fee']
                : (isset($hoaPayload['min_fee']) ? (int) $hoaPayload['min_fee'] : null),
            hoaMaxFee: isset($payload['hoa_max_fee'])
                ? (int) $payload['hoa_max_fee']
                : (isset($hoaPayload['max_fee']) ? (int) $hoaPayload['max_fee'] : null),
            hoaSamples: isset($payload['hoa_samples'])
                ? (int) $payload['hoa_samples']
                : (isset($hoaPayload['n_samples']) ? (int) $hoaPayload['n_samples'] : null),
            hoaSubdivision: $payload['hoa_subdivision'] ?? ($hoaPayload['subdivision'] ?? null),
            hoaSubdivisionId: $payload['hoa_subdivision_id'] ?? ($hoaPayload['subdivision_id'] ?? null),
            schoolDistrict: $payload['school_district']
                ?? data_get($schoolPayload, 'district')
                ?? data_get($schoolPayload, 'school_district'),
            elementarySchool: $payload['elementary_school']
                ?? data_get($schoolPayload, 'elementary.0.name'),
            middleSchool: $payload['middle_school']
                ?? data_get($schoolPayload, 'middle.0.name'),
            highSchool: $payload['high_school']
                ?? data_get($schoolPayload, 'high.0.name'),
            hasPool: array_key_exists('has_pool', $payload)
                ? self::normalizeBool($payload['has_pool'])
                : self::normalizeBool(data_get($detailsProperty, 'pool')),
            hasAttic: array_key_exists('has_attic', $payload)
                ? self::normalizeBool($payload['has_attic'])
                : self::normalizeBool(data_get($detailsProperty, 'attic')),
            basementType: $payload['basement_type'] ?? data_get($detailsProperty, 'basement'),
            hasAirConditioning: array_key_exists('has_air_conditioning', $payload)
                ? self::normalizeBool($payload['has_air_conditioning'])
                : self::normalizeBool(data_get($detailsProperty, 'air_conditioning')),
            fireplaceType: $payload['fireplace_type'] ?? data_get($detailsProperty, 'fireplace'),
            payload: $payload['payload'] ?? [],
            fetchedAt: CarbonImmutable::parse($payload['fetched_at'] ?? 'now'),
            expiresAt: CarbonImmutable::parse($payload['expires_at'] ?? 'now'),
            status: (string) ($payload['status'] ?? 'ready'),
            errorCode: $payload['error_code'] ?? null,
            errorMessage: $payload['error_message'] ?? null,
        );
    }

    private static function normalizeBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

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
}
