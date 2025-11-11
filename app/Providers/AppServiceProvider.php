<?php

namespace App\Providers;

use App\Application\Shared\Listeners\IncrementFeatureUsage;
use App\Domain\Appraisal\Providers\AppraisalProviderInterface;
use App\Domain\Appraisal\Repositories\PropertyWorthRepositoryInterface;
use App\Domain\GlowUp\Contracts\GlowUpImageProvider;
use App\Infrastructure\GlowUp\Providers\FakeAiImageService;
use App\Infrastructure\GlowUp\Providers\ReplicateImageService;
use Illuminate\Auth\Events\Verified;
use App\Http\Requests\Auth\VerifyEmailRequest as AppVerifyEmailRequest;
use App\Http\Responses\RedirectAsIntended as AppRedirectAsIntended;
use App\Http\Responses\PasswordResetResponse as AppPasswordResetResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Domain\Shared\Events\FeatureUsed;
use App\Infrastructure\Appraisal\Persistence\EloquentPropertyWorthRepository;
use App\Infrastructure\Appraisal\Providers\HouseCanaryProvider;
use App\Infrastructure\Appraisal\Providers\MockAppraisalProvider;
use Laravel\Fortify\Contracts\PasswordResetResponse as FortifyPasswordResetResponse;
use Laravel\Fortify\Http\Responses\RedirectAsIntended as FortifyRedirectAsIntended;
use Laravel\Fortify\Http\Requests\VerifyEmailRequest as FortifyVerifyEmailRequest;

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
            PropertyWorthRepositoryInterface::class,
            EloquentPropertyWorthRepository::class,
        );
        // todo moverlo al controlador
        $this->app->bind(
            GlowUpImageProvider::class,
            function ($app) {
                $provider = config('services.glowup.provider', 'fake');

                return match ($provider) {
                    'replicate' => $app->make(ReplicateImageService::class),
                    default => $app->make(FakeAiImageService::class),
                };
            }
        );
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
        $this->app->bind(
            FortifyRedirectAsIntended::class,
            AppRedirectAsIntended::class,
        );
        $this->app->bind(
            FortifyVerifyEmailRequest::class,
            AppVerifyEmailRequest::class,
        );
        $this->app->bind(
            FortifyPasswordResetResponse::class,
            AppPasswordResetResponse::class,
        );
        if ($this->app->environment(['local', 'staging']) && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }
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

        Event::listen(
            FeatureUsed::class,
            [IncrementFeatureUsage::class, 'handle'],
        );
    }
}
