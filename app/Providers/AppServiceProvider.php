<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Billing\Repositories\BillingRepositoryInterface::class,
            \App\Infrastructure\Billing\Persistence\EloquentBillingRepository::class
        );
        $this->app->bind(
            \App\Domain\Auth\Repositories\UserRepositoryInterface::class,
            \App\Infrastructure\Auth\Persistence\EloquentUserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
