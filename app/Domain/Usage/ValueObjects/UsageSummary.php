<?php

namespace App\Domain\Usage\ValueObjects;

use Carbon\CarbonInterface;

final class UsageSummary
{
    public function __construct(
        public readonly string $tier,
        public readonly string $planLabel,
        public readonly ?int $limit,
        public readonly int $used,
        public readonly ?int $remaining,
        public readonly string $periodKey,
        public readonly ?CarbonInterface $resetsAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'plan' => [
                'tier' => $this->tier,
                'label' => $this->planLabel,
                'limit' => $this->limit,
            ],
            'used' => $this->used,
            'remaining' => $this->remaining,
            'limit' => $this->limit,
            'period_key' => $this->periodKey,
            'resets_at' => $this->resetsAt?->toIso8601String(),
        ];
    }
}
