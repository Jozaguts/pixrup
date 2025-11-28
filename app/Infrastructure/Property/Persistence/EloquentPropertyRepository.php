<?php

namespace App\Infrastructure\Property\Persistence;

use App\Domain\Properties\Entities\PropertyEntity;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Models\Property;

class EloquentPropertyRepository implements PropertyRepositoryInterface
{

    public function save(PropertyEntity $property): PropertyEntity
    {
        $model = Property::create([
            'title' => $property->address,
            'status' => 'in-progress',
            'address' => $property->address,
            'city' => $property->city,
            'state' => $property->state ,
            'postal_code' => $property->postal_code,
            'country' => $property->country,
            'lat' => $property->lat,
            'lng' => $property->lng,
            'place_id' => $property->place_id,
            'metadata' => [
                'source' => 'ui',
                'created_via' => 'wizard',
            ],
            'property_type' => $property->property_type,
            'bedrooms' => $property->bedrooms,
            'bathrooms' => $property->bathrooms,
            'square_footage' => $property->square_footage,
        ]);
        return new PropertyEntity(
            $model->id,
            $model->address,
            $model->status,
            $model->address,
            $model->city,
            $model->state,
            $model->postal_code,
            $model->country,
            $model->lat,
            $model->lng,
            $model->place_id,
            $model->metadata,
            $model->property_type,
            $model->bedrooms,
            $model->bathrooms,
            $model->square_footage,
        );
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