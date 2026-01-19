<?php

namespace App\Infrastructure\Property\Overview\Persistence;

use App\Application\Properties\Overview\Contracts\OverviewRepository;
use App\Application\Properties\Overview\DTOs\PropertyOverviewSnapshotDTO;
use App\Models\PropertyOverview;
use Carbon\CarbonImmutable;

final class EloquentOverviewRepository implements OverviewRepository
{
    public function get(int $propertyId): ?PropertyOverviewSnapshotDTO
    {
        $row = PropertyOverview::query()
            ->where('property_id', $propertyId)
            ->orderByDesc('fetched_at')
            ->first();

        if (!$row) {
            return null;
        }

        if ($row->expires_at && $row->expires_at->isPast()) {
            return null;
        }

        return new PropertyOverviewSnapshotDTO(
            propertyId: $row->property_id,
            provider: $row->provider,
            ownerOccupied: $row->owner_occupied,
            femaDisasterArea: $row->fema_disaster_area,
            floodZone: $row->flood_zone,
            floodRisk: $row->flood_risk,
            crimePercentile: $row->crime_percentile,
            crimeCompareScope: $row->crime_compare_scope,
            msa: $row->msa,
            msaName: $row->msa_name,
            censusTract: $row->census_tract,
            blockGroup: $row->block_group,
            hoaAnnualEstimate: $this->intFromPayload($row->payload, 'hoa_est.association_estimated.annual_hoa_est'),
            hoaMinFee: $this->intFromPayload($row->payload, 'hoa_est.association_estimated.min_fee'),
            hoaMaxFee: $this->intFromPayload($row->payload, 'hoa_est.association_estimated.max_fee'),
            hoaSamples: $this->intFromPayload($row->payload, 'hoa_est.association_estimated.n_samples'),
            hoaSubdivision: $this->stringFromPayload($row->payload, 'hoa_est.association_estimated.subdivision'),
            hoaSubdivisionId: $this->stringFromPayload($row->payload, 'hoa_est.association_estimated.subdivision_id'),
            schoolDistrict: $this->stringFromPayload($row->payload, 'school.result.school.district')
                ?? $this->stringFromPayload($row->payload, 'school.result.school.school_district'),
            elementarySchool: $this->stringFromPayload($row->payload, 'school.result.school.elementary.0.name'),
            middleSchool: $this->stringFromPayload($row->payload, 'school.result.school.middle.0.name'),
            highSchool: $this->stringFromPayload($row->payload, 'school.result.school.high.0.name'),
            hasPool: $this->boolFromPayload($row->payload, 'details.property.pool'),
            hasAttic: $this->boolFromPayload($row->payload, 'details.property.attic'),
            basementType: $this->stringFromPayload($row->payload, 'details.property.basement'),
            hasAirConditioning: $this->boolFromPayload($row->payload, 'details.property.air_conditioning'),
            fireplaceType: $this->stringFromPayload($row->payload, 'details.property.fireplace'),
            payload: $row->payload ?? [],
            fetchedAt: CarbonImmutable::parse($row->fetched_at ?? $row->updated_at ?? 'now'),
            expiresAt: CarbonImmutable::parse($row->expires_at ?? $row->updated_at ?? 'now'),
            status: $row->status ?? 'ready',
            errorCode: $row->error_code,
            errorMessage: $row->error_message,
        );
    }

    public function save(PropertyOverviewSnapshotDTO $dto): void
    {
        PropertyOverview::query()->updateOrCreate(
            ['property_id' => $dto->propertyId, 'provider' => $dto->provider],
            [
                'status' => $dto->status,
                'owner_occupied' => $dto->ownerOccupied,
                'fema_disaster_area' => $dto->femaDisasterArea,
                'flood_zone' => $dto->floodZone,
                'flood_risk' => $dto->floodRisk,
                'crime_percentile' => $dto->crimePercentile,
                'crime_compare_scope' => $dto->crimeCompareScope,
                'msa' => $dto->msa,
                'msa_name' => $dto->msaName,
                'census_tract' => $dto->censusTract,
                'block_group' => $dto->blockGroup,
                'payload' => $dto->payload,
                'fetched_at' => $dto->fetchedAt->toDateTimeString(),
                'expires_at' => $dto->expiresAt->toDateTimeString(),
                'error_code' => $dto->errorCode,
                'error_message' => $dto->errorMessage,
            ]
        );
    }

    public function forget(int $propertyId): void
    {
        PropertyOverview::query()
            ->where('property_id', $propertyId)
            ->delete();
    }

    private function intFromPayload(?array $payload, string $path): ?int
    {
        $value = data_get($payload ?? [], $path);
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) round((float) $value) : null;
    }

    private function stringFromPayload(?array $payload, string $path): ?string
    {
        $value = data_get($payload ?? [], $path);
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function boolFromPayload(?array $payload, string $path): ?bool
    {
        $value = data_get($payload ?? [], $path);
        if ($value === null || $value === '') {
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
