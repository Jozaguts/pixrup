<?php

namespace App\Http\Middleware;

use App\Application\Usage\Services\UsageSummaryService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $user = $request->user();
        $planUsage = null;
        $userPayload = $user?->toArray();

        if ($user !== null) {
            $planUsage = app(UsageSummaryService::class)->forUser($user)->toArray();

            if ($userPayload !== null && $planUsage !== null) {
                $userPayload['property_usage_limit'] = $planUsage['limit'];
                $userPayload['property_usage_count'] = $planUsage['used'];
                $userPayload['usage_reset_at'] = $planUsage['resets_at'] ?? null;
                $userPayload['plan_usage'] = $planUsage;
            }
        }
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $userPayload,
            ],
            'planUsage' => $planUsage,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail && ! $request->user()?->hasVerifiedEmail(),
            'flash' => [
                'status' => $request->session()->get('status'),
                'glowupJob' => $request->session()->get('glowupJob'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
