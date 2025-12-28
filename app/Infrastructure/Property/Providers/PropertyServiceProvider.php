<?php

namespace App\Infrastructure\Property\Providers;

use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Properties\Repositories\PropertyPhotoRepositoryInterface;
use App\Domain\Properties\Repositories\PropertyRepositoryInterface;
use App\Domain\Properties\Repositories\ICacheStore;
use App\Domain\Properties\Repositories\SpyHuntMarketDataProviderInterface;
use App\Infrastructure\Property\Persistence\EloquentPropertyPhotoRepository;
use App\Infrastructure\Property\Persistence\EloquentPropertyRepository;
use App\Infrastructure\Property\Persistence\SpyHuntCacheRepository;
use App\Infrastructure\Property\Persistence\EloquentSpyHuntRepository;
use App\Infrastructure\Property\Persistence\CompositeCacheRepository;
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
            ICacheStore::class, function($app){
                return new CompositeCacheRepository(
                    $app->make(SpyHuntCacheRepository::class),
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
    }
}