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
        );
    }
}