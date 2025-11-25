<?php

namespace App\Application\Properties\DTOs;

class ComparableDTO
{
    /**
     * @param array $saleComps   Lista de comparables de venta
     * @param array|null $rentComps Lista de comparables de renta
     */
    public function __construct(
        public array $saleComps,
        public ?array $rentComps = null,
    ){}
    public function toArray(): array
    {
        return [
           'summary' => [
               'sale_count' => count($this->saleComps),
               'rent_count' => $this->rentComps ? count($this->rentComps) : 0,
           ],
            'sale' => $this->saleComps,
            'rent' => $this->rentComps,
        ];
    }
}