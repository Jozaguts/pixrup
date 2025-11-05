<?php

/**
 * Description: File providing an Eloquent-backed repository for property worth persistence.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Implements the property worth repository interface using Laravel models.
 */

namespace App\Infrastructure\Appraisal\Persistence;

use App\Application\Appraisal\DTOs\PropertyWorthDTO;
use App\Domain\Appraisal\Entities\PropertyWorth as PropertyWorthEntity;
use App\Domain\Appraisal\Repositories\PropertyWorthRepositoryInterface;
use App\Models\PropertyWorth;
use Carbon\Carbon;

/**
 * Description: Repository adapter persisting property worth data via Eloquent ORM.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Enables application services to retrieve and store valuations.
 */
class EloquentPropertyWorthRepository implements PropertyWorthRepositoryInterface
{
    /**
     * Description: Fetch the latest valuation within the freshness threshold for a property.
     * Parameters: int $propertyId Property identifier; Carbon $threshold Earliest acceptable fetched_at timestamp.
     * Returns: ?PropertyWorthEntity
     * Expected Result: Returns domain entity when a fresh record exists, otherwise null.
     */
    public function findLatestWithin(int $propertyId, Carbon $threshold): ?PropertyWorthEntity
    {
        $record = PropertyWorth::query()
            ->where('property_id', $propertyId)
            ->orderByDesc('fetched_at')
            ->orderByDesc('id')
            ->first();

        if ($record === null) {
            return null;
        }

        $fetchedAt = $record->fetched_at
            ? Carbon::parse($record->fetched_at)
            : Carbon::parse($record->updated_at);

        if ($fetchedAt->lessThan($threshold)) {
            return null;
        }

        return $this->mapModelToEntity($record);
    }

    /**
     * Description: Persist a valuation record for the property using provided DTO data.
     * Parameters: int $propertyId Property identifier; PropertyWorthDTO $dto Normalized valuation payload.
     * Returns: PropertyWorthEntity
     * Expected Result: Newly stored valuation represented as a domain entity.
     */
    public function saveFromDto(int $propertyId, PropertyWorthDTO $dto): PropertyWorthEntity
    {
        $record = new PropertyWorth();
        $record->property_id = $propertyId;
        $record->value = $dto->value;
        $record->value_low = $dto->value_low;
        $record->value_high = $dto->value_high;
        $record->confidence = (int) round($dto->confidence * 100);
        $record->comparables = $dto->comparables;
        $record->provider = $dto->provider;
        $record->fetched_at = $dto->fetched_at;
        $record->save();

        return $this->mapModelToEntity($record);
    }

    /**
     * Description: Convert an Eloquent model instance into a domain entity representation.
     * Parameters: PropertyWorth $model Persisted model instance.
     * Returns: PropertyWorthEntity
     * Expected Result: Domain entity mirrors the model's persisted state.
     */
    private function mapModelToEntity(PropertyWorth $model): PropertyWorthEntity
    {
        return new PropertyWorthEntity(
            value: (float) $model->value,
            value_low: (float) $model->value_low,
            value_high: (float) $model->value_high,
            confidence: ((float) $model->confidence) / 100,
            comparables: $model->comparables ?? [],
            provider: (string) $model->provider,
            fetched_at: Carbon::parse($model->fetched_at ?? $model->created_at),
            created_at: Carbon::parse($model->created_at),
            updated_at: Carbon::parse($model->updated_at),
        );
    }
}
