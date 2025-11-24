<?php

namespace App\Application\Properties\DTOs;

class ComparablesDTO
{
    /**
     * @param int $salesCompsCount
     * @param int $rentCompsCount
     * @param ComparableItemDTO $items
     */
    public function __construct(
        public int $salesCompsCount,
        public int $rentCompsCount,
        public ComparableItemDTO $items,
    ){}
    public function toArray(): array
    {
        return [
            'salesCompsCount' => $this->salesCompsCount,
            'rentCompsCount' => $this->rentCompsCount,
            'items' => $this->items->toArray(),
        ];
    }
}