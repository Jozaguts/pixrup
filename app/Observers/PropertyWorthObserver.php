<?php

namespace App\Observers;

use App\Domain\PixWorth\Jobs\GeneratePixWorthRunesJob;

class PropertyWorthObserver
{
    public function created(object $propertyWorth): void
    {
        GeneratePixWorthRunesJob::dispatch($propertyWorth);
    }

    public function updated(object $propertyWorth): void
    {
        GeneratePixWorthRunesJob::dispatch($propertyWorth);
    }
}
