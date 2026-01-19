<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withEvents(discover: [
        __DIR__.'/../app/Interface/*/Events',
        __DIR__.'/../app/Interface/*/Listeners',
        __DIR__.'/../app/Domain/*/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state'])
            ->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class])
            ->alias([
                'verified' => EnsureEmailIsVerified::class,
                'localize'                => LaravelLocalizationRoutes::class,
                'localizationRedirect'    => LaravelLocalizationRedirectFilter::class,
                'localeSessionRedirect'   => LocaleSessionRedirect::class,
                'localeCookieRedirect'    => LocaleCookieRedirect::class,
                'localeViewPath'          => LaravelLocalizationViewPath::class])
            ->redirectGuestsTo(fn (Request $request) => route('auth.register.show'))
            ->validateCsrfTokens(except: [
                'stripe/webhook',
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidSignatureException $exception, Request $request) {
            return redirect()->route('verification.notice')->with('status', 'verification-link-invalid');
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->routeIs('verification.verify') || $request->is('email/verify/*')) {
                if ($exception->response()) {
                    return $exception->response();
                }

                return redirect()->route('verification.notice')->with('status', 'verification-link-invalid');
            }

            return null;
        });

        $exceptions->render(function (RouteNotFoundException $exception, Request $request) {
            if (str_contains($exception->getMessage(), 'Route [login]')) {
                return redirect()->route('auth.login.show');
            }

            return null;
        });
    })->create();
