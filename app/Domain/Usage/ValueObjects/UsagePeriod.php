<?php

namespace App\Domain\Usage\ValueObjects;

use Carbon\CarbonImmutable;

final class UsagePeriod
{
    public function __construct(
        public readonly string $key,
        public readonly CarbonImmutable $startsAt,
        public readonly CarbonImmutable $endsAt,
        public readonly CarbonImmutable $resetsAt,
    ) {
    }
}
