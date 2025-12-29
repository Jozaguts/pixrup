<?php

namespace App\Infrastructure\Property\Providers;

use App\Application\Properties\SpyHunt\Contracts\SpyHuntCache;
use App\Application\Shared\Contracts\Cache\KeyValueStore;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Properties\Repositories\PropertyPhotoRepositoryInterface;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Infrastructure\Property\Persistence\CompositeCacheRepository;
use App\Infrastructure\Property\Persistence\EloquentPropertyPhotoRepository;
use App\Infrastructure\Property\Persistence\EloquentPropertyRepository;
use App\Infrastructure\Property\SpyHunt\Persistence\EloquentSpyHuntRepository;
use App\Infrastructure\Property\SpyHunt\Persistence\RedisSpyHuntCache;
use App\Infrastructure\Shared\Persistence\RedisKeyValueStore;
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
            SpyHuntCache::class, function($app){
                return new CompositeCacheRepository(
                    $app->make(RedisSpyHuntCache::class),
                    $app->make(EloquentSpyHuntRepository::class)
                );
        });
        $this->app->bind(
            AppraisalProviderInterface::class,
            function ($app) {
                $provider = config('services.appraisal.provider', 'mock');

                return match ($provider) {
                    'housecanary' => $app->make(HouseCanaryProvider::class),
                    default => $app->make(MockAppraisalProvider::class),
                };
            }
        );
        $this->app->bind(KeyValueStore::class, RedisKeyValueStore::class);
    }
}