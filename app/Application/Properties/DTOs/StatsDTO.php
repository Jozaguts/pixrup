<?php

namespace App\Application\Properties\DTOs;

class StatsDTO
{
    public function __construct(
        public int $radiusMatches,
        public ?float $zipDom,
        public ?int $buyerDemandScore = null,
        public ?int $buyerDemandChange = null,
        public ?int $subjectDom = null,
    ){}

    public function toArray(): array
    {
        return [
            'radiusMatches' => $this->radiusMatches,
            'buyerDemandScore' => $this->buyerDemandScore,
            'buyerDemandChange' => $this->buyerDemandChange,
            'subjectDom' => $this->subjectDom,
            'zipDom' => $this->zipDom,
        ];
    }

}