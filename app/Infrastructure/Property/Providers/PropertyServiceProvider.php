<?php

namespace App\Infrastructure\Property\Providers;

use App\Domain\Properties\Repositories\PropertyPhotoRepositoryInterface;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Infrastructure\Property\Persistence\EloquentPropertyRepository;
use App\Infrastructure\Property\Persistence\EloquentPropertyPhotoRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
class PropertyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PropertyRepositoryInterface::class,
            EloquentPropertyRepository::class
        );
        $this->app->bind(
            PropertyPhotoRepositoryInterface::class,
            EloquentPropertyPhotoRepository::class
        );
    }
}