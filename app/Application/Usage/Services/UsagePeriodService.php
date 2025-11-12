<?php

namespace App\Application\Usage\Services;

use App\Domain\Usage\ValueObjects\UsagePeriod;
use Carbon\CarbonImmutable;

class UsagePeriodService
{
    private string $timezone;

    public function __construct(?string $timezone = null)
    {
        $this->timezone = $timezone ?? config('usage.timezone', 'UTC');
    }

    public function current(): UsagePeriod
    {
        $now = CarbonImmutable::now($this->timezone);
        $start = $now->startOfMonth();
        $nextMonthStart = $start->addMonth();
        $end = $nextMonthStart->subSecond();

        return new UsagePeriod(
            $start->format('Y-m'),
            $start->setTimezone('UTC'),
            $end->setTimezone('UTC'),
            $nextMonthStart->setTimezone('UTC'),
        );
    }
}
