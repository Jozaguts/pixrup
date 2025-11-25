<?php

namespace App\Infrastructure\Property\Providers;

use App\Domain\Properties\Repositories\PropertyPhotoRepositoryInterface;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Domain\Properties\Repositories\SpyHuntCacheRepositoryInterface;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Infrastructure\Property\Persistence\EloquentPropertyRepository;
use App\Infrastructure\Property\Persistence\EloquentPropertyPhotoRepository;
use App\Infrastructure\Property\Persistence\RedisSpyHuntCacheRepository;
use App\Infrastructure\Property\Repositories\SpyHuntCacheRepository;
use Illuminate\Support\ServiceProvider;
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
        $this->app->bind(
            SpyHuntMarketDataProviderInterface::class,
            fn () => new RentCastMarketDataProvider(config('services.rentcast.api_key'))
        );
        $this->app->bind(
            SpyHuntCacheRepositoryInterface::class,
            SpyHuntCacheRepository::class
        );
        $this->app->bind(
            SpyHuntCacheRepositoryInterface::class,
            fn() => new RedisSpyHuntCacheRepository()
        );
    }
}