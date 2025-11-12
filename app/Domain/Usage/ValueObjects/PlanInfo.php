<?php

namespace App\Domain\Usage\ValueObjects;

final class PlanInfo
{
    public function __construct(
        public readonly string $tier,
        public readonly string $label,
        public readonly ?int $limit,
    ) {
    }
}
