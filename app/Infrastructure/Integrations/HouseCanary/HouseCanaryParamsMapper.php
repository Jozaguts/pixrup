<?php

namespace App\Infrastructure\Integrations\HouseCanary;

use App\Domain\Properties\Entities\PropertyEntity;

final class HouseCanaryParamsMapper
{
    public function fromProperty(PropertyEntity $property): array
    {
        $suffix = ", {$property->city}, {$property->state} {$property->postal_code}";
        $street = str_ends_with($property->address, $suffix)
            ? substr($property->address, 0, -strlen($suffix))
            : $property->address;

        return [
            'address' => $street,
            'city' => $property->city,
            'state' => $property->state,
            'zipcode' => $property->postal_code,
        ];
    }
}