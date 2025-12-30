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
}
