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
}