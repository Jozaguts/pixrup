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
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $planUsage = null;
        $userPayload = $user?->toArray();
        $limitExceeded = false;

        if ($user !== null) {
            $planUsage = app(UsageSummaryService::class)->forUser($user)->toArray();

            if ($userPayload !== null && $planUsage !== null) {
                $docsUsage = $planUsage['usage']['docs'] ?? null;
                $userPayload['property_usage_limit'] = $docsUsage['limit'] ?? null;
                $userPayload['property_usage_count'] = $docsUsage['used'] ?? null;
                $userPayload['usage_reset_at'] = $planUsage['resets_at'] ?? null;
                $userPayload['plan_usage'] = $planUsage;
            }

            if ($planUsage !== null) {
                foreach (['docs', 'renders'] as $bucket) {
                    $usage = $planUsage['usage'][$bucket] ?? null;
                    if (! is_array($usage)) {
                        continue;
                    }

                    $limit = (int) ($usage['limit'] ?? 0);
                    $used = (int) ($usage['used'] ?? 0);

                    if ($limit > 0 && $used >= $limit) {
                        $limitExceeded = true;
                        break;
                    }
                }
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $userPayload,
            ],
            'planUsage' => $planUsage,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail && ! $request->user()?->hasVerifiedEmail(),
            'flash' => [
                'status' => $request->session()->get('status'),
                'glowupJob' => $request->session()->get('glowupJob'),
                'limitExceeded' => $limitExceeded,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
