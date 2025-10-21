<?php

namespace App\Providers;

use Illuminate\Auth\Events\Verified;
use App\Http\Responses\RedirectAsIntended as AppRedirectAsIntended;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Http\Responses\RedirectAsIntended as FortifyRedirectAsIntended;

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
        $this->app->bind(
            FortifyRedirectAsIntended::class,
            AppRedirectAsIntended::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Verified::class, function (Verified $event): void {
            Log::info('User email verified', [
                'user_id' => $event->user->getAuthIdentifier(),
                'email' => $event->user->getEmailForVerification(),
                'timestamp' => now()->toIso8601String(),
            ]);
        });
    }
}
