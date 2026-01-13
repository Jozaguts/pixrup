<?php

namespace App\Infrastructure\Property\Persistence;

use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Models\Property;

class EloquentPropertyRepository implements PropertyRepositoryInterface
{

    public function save(PropertyEntity $propertyEntity): PropertyEntity
    {
        $property = Property::create([
            'title' => $propertyEntity->address,
            'user_id' => auth()->user()->id,
            'status' => 'in-progress',
            'address' => $propertyEntity->address,
            'city' => $propertyEntity->city,
            'state' => $propertyEntity->state ,
            'postal_code' => $propertyEntity->postal_code,
            'country' => $propertyEntity->country,
            'lat' => $propertyEntity->lat,
            'lng' => $propertyEntity->lng,
            'place_id' => $propertyEntity->place_id,
            'metadata' => $propertyEntity->metadata,
            'property_type' => $propertyEntity->property_type,
            'bedrooms' => $propertyEntity->bedrooms,
            'bathrooms' => $propertyEntity->bathrooms,
            'square_footage' => $propertyEntity->square_footage,
        ]);

        return $property->toEntity();
    }

    public function findOrFail(int $id): PropertyEntity
    {
        $property = Property::where(['id' => $id])->firstOrFail();
        return new PropertyEntity(
            $property->id,
            $property->address,
            $property->status,
            $property->address,
            $property->city,
            $property->state,
            $property->postal_code,
            $property->country,
            $property->lat,
            $property->lng,
            $property->place_id,
            $property->metadata,
            $property->property_type,
            $property->bedrooms,
            $property->bathrooms,
            $property->square_footage,
        );
    }
    public function findById(int $id): ?PropertyEntity
    {
        $property = Property::find($id);

        return new PropertyEntity(
            $property->id,
            auth()->user()->id,
            $property->address,
            $property->status,
            $property->address,
            $property->city,
            $property->state,
            $property->postal_code,
            $property->country,
            $property->lat,
            $property->lng,
            $property->place_id,
            $property->metadata,
            $property->property_type,
            $property->bedrooms,
            $property->bathrooms,
            $property->square_footage,
        );
    }
}
