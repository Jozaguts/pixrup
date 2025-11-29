<?php

namespace App\Application\Properties\DTOs;

class FiltersDTO
{
    public function __construct(
        public array $radiusOptions,
        public array $propertyTypes ,
        public float $priceMin,
        public float $priceMax,
        public float $defaultRadius,
        public string $defaultPropertyType,
        public string $defaultMode,
    ) {
    }
    public function toArray(): array
    {
        return [
            'radius' => $this->radiusOptions,
            'property_types' => $this->propertyTypes,
            'price_range' => [
                'min' => $this->priceMin,
                'max' => $this->priceMax,
            ],
            'defaults' => [
                'radius' => $this->defaultRadius,
                'property_type' => $this->defaultPropertyType,
                'mode' => $this->defaultMode,
            ],
        ];
    }
    public static function defaults(?array $array = []): FiltersDTO
    {
        return new FiltersDTO(
            radiusOptions: $array['radiusOption'] ??[1,3,5],
            propertyTypes:  $array['propertyTypes'] ?? ['Single Family','Condo','Townhouse','Manufactured','Multi-Family','Apartment','Land'],
            priceMin: $array['priceMin'] ?? 50000,
            priceMax: $array['priceMax'] ?? 3000000,
            defaultRadius: $array['defaultRadius']?? 3,
            defaultPropertyType: $array['defaultPropertyType']?? 'Single Family',
            defaultMode: $array['defaultMode'] ?? 'sale'
        );
    }

}