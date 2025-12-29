<?php

/**
 * Description: File providing an Eloquent-backed repository for property worth persistence.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Implements the property worth repository interface using Laravel models.
 */

namespace App\Infrastructure\Property\PixrWorth\Persistence;

use App\Application\Properties\DTOs\PropertyWorthDTO;
use App\Application\Properties\PixrWorth\Contracts\WorthRepository;
use App\Domain\Appraisal\Entities\AppraisalSnapshot as PropertyWorthEntity;
use App\Models\PropertyWorth;
use Carbon\Carbon;

/**
 * Description: Repository adapter persisting property worth data via Eloquent ORM.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Enables application services to retrieve and store valuations.
 */
final class EloquentWorthRepository implements WorthRepository
{
    /**
     * Description: Fetch the latest valuation within the freshness threshold for a property.
     * Parameters: int $propertyId Property identifier; Carbon $threshold Earliest acceptable fetched_at timestamp.
     * Returns: ?PropertyWorthEntity
     * Expected Result: Returns domain entity when a fresh record exists, otherwise null.
     */
    public function findFresh(int $propertyId, \DateTimeInterface $threshold): ?PropertyWorthDTO
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

        return new PropertyWorthDTO(
            value: $record->value,
            value_low: $record->value_low,
            value_high: $record->value_high,
            confidence: $record->confidence,
            comparables: $record->comparables,
            provider: $record->provider,
            fetched_at: $record->fetched_at,
            cached_at: $fetchedAt,
        );
    }

    /**
     * Description: Persist a valuation record for the property using provided DTO data.
     * Parameters: int $propertyId Property identifier; PropertyWorthDTO $dto Normalized valuation payload.
     * Returns: PropertyWorthEntity
     * Expected Result: Newly stored valuation represented as a domain entity.
     */
    public function save(int $propertyId, PropertyWorthDTO $dto): void
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
    }
}
