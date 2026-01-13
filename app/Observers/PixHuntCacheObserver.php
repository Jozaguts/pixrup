<?php

namespace App\Observers;

use App\Domain\PixHunt\Jobs\GeneratePixHuntRunesJob;

class PixHuntCacheObserver
{
    public function created(object $model): void
    {
        GeneratePixHuntRunesJob::dispatch($model);
    }

    public function updated(object $model): void {
        GeneratePixHuntRunesJob::dispatch($model);
    }
}
