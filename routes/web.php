<?php


use App\Models\Property;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Interface\Auth\Http\Controllers\AuthController;
use App\Interface\Auth\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/properties/new', function () {
    return Inertia::render('properties/New');
})->middleware(['auth', 'verified'])->name('properties.new');

Route::get('/properties/{property}', function (Property $property) {
    $property->load('latestWorth');

    $latestWorth = $property->latestWorth;

    $worthPayload = $latestWorth ? [
        'id' => $latestWorth->id,
        'value' => $latestWorth->value,
        'confidence' => $latestWorth->confidence,
        'comparables' => $latestWorth->comparables,
        'trend' => $latestWorth->trend,
        'provider' => $latestWorth->provider,
        'fetched_at' => optional($latestWorth->fetched_at)->toIso8601String(),
    ] : null;

    $propertyData = [
        'id' => $property->id,
        'title' => $property->title ?? $property->address,
        'status' => $property->status ?? 'in-progress',
        'address' => [
            'line1' => $property->address,
            'city' => $property->city,
            'state' => $property->state,
            'postal_code' => $property->postal_code,
        ],
        'owner' => [
            'name' => 'Workspace Owner',
            'email' => auth()->user()?->email,
        ],
        'summary' => [
            'bedrooms' => data_get($property->metadata, 'summary.bedrooms'),
            'bathrooms' => data_get($property->metadata, 'summary.bathrooms'),
            'livingArea' => data_get($property->metadata, 'summary.livingArea'),
            'lotSize' => data_get($property->metadata, 'summary.lotSize'),
            'yearBuilt' => data_get($property->metadata, 'summary.yearBuilt'),
            'propertyType' => data_get($property->metadata, 'summary.propertyType'),
        ],
        'pricing' => [
            'acquisition' => data_get($property->metadata, 'pricing.acquisition'),
            'currentEstimate' => $latestWorth?->value,
            'potentialAfterGlow' => $latestWorth ? round($latestWorth->value * 1.06) : null,
        ],
        'last_updated' => optional($property->updated_at)->toIso8601String(),
        'last_updated_human' => optional($property->updated_at)->diffForHumans(),
        'tags' => data_get($property->metadata, 'tags', []),
        'worth' => $worthPayload,
        'workspace' => [
            'actions' => [
                ['id' => 'appraise', 'label' => 'Appraise', 'module' => 'pixrWorth'],
                ['id' => 'glowUp', 'label' => 'Glow-Up', 'module' => 'pixrGlowUp'],
                ['id' => 'spyHunt', 'label' => 'SpyHunt', 'module' => 'pixrSpyHunt'],
                ['id' => 'vision', 'label' => '3D Tour', 'module' => 'pixrVision'],
                ['id' => 'seal', 'label' => 'Report', 'module' => 'pixrSeal'],
                ['id' => 'collab', 'label' => 'Collab', 'module' => 'pixrCollab'],
            ],
            'modules' => [
                'overview' => [
                    'endpoint' => "/api/properties/{$property->id}",
                    'status' => 'ready',
                    'last_run_at' => optional($property->updated_at)->toIso8601String(),
                ],
                'pixrWorth' => [
                    'endpoint' => "/api/properties/{$property->id}/worth",
                    'status' => $latestWorth ? 'ready' : 'needs-action',
                    'last_run_at' => optional($latestWorth?->fetched_at)->toIso8601String(),
                ],
                'pixrGlowUp' => [
                    'endpoint' => "/api/properties/{$property->id}/glowup/jobs",
                    'status' => 'ready',
                    'last_run_at' => now()->subHours(12)->toIso8601String(),
                ],
                'pixrSpyHunt' => [
                    'endpoint' => "/api/properties/{$property->id}/spyhunt",
                    'status' => 'processing',
                    'last_run_at' => now()->subMinutes(45)->toIso8601String(),
                ],
                'pixrVision' => [
                    'endpoint' => "/api/properties/{$property->id}/vision",
                    'status' => 'ready',
                    'last_run_at' => now()->subDays(5)->toIso8601String(),
                ],
                'pixrSeal' => [
                    'endpoint' => "/api/properties/{$property->id}/report",
                    'status' => $latestWorth ? 'ready' : 'draft',
                    'last_run_at' => null,
                ],
                'pixrCollab' => [
                    'endpoint' => "/api/properties/{$property->id}/collab/token",
                    'status' => 'ready',
                    'last_run_at' => now()->subMinutes(5)->toIso8601String(),
                ],
            ],
        ],
    ];

    return Inertia::render('properties/Show', [
        'property' => $propertyData,
    ]);
})->middleware(['auth', 'verified'])->name('properties.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.store');

Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
require __DIR__.'/settings.php';


