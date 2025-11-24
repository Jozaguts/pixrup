<?php

namespace App\Application\Properties\DTOs;

class SpyHuntDataDTO
{
    public function __construct(
        public readonly PropertyDto $property,
        public readonly FiltersDTO $filters,
        public readonly MarketSnapshotDTO $marketSnapshot,
        public readonly ValueEstimateDTO $valueEstimate,
        public readonly ComparablesDTO $comparables,
        public readonly StatsDTO $stats,
        public readonly SourceDTO $source,

    ) {
    }
    public function toArray(): array
    {
        return [
            'property' => $this->property->toArray(),
            'filters' => $this->filters->toArray(),
            'market_snapshot' => $this->marketSnapshot->toArray(),
            'value_estimate' => $this->valueEstimate->toArray(),
            'comps' => $this->comparables->toArray(),
            'stats' => $this->stats->toArray(),
            'source' => $this->source->toArray(),
        ];
    }
}