<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Interface\Auth\Http\Controllers\AuthController;
use App\Interface\Auth\Http\Controllers\SocialAuthController;
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

Route::get('/properties/{property}', function (string $property) {
    $propertyId = (int) $property ?: 1;

    $propertyData = [
        'id' => $propertyId,
        'title' => 'Modern Craftsman Estate',
        'status' => 'in-progress',
        'address' => [
            'line1' => '123 Main St',
            'city' => 'Austin',
            'state' => 'TX',
            'postal_code' => '78701',
        ],
        'owner' => [
            'name' => 'Marisol Vega',
            'email' => 'marisol@example.com',
        ],
        'summary' => [
            'bedrooms' => 4,
            'bathrooms' => 3.5,
            'livingArea' => 2980,
            'lotSize' => 0.34,
            'yearBuilt' => 2012,
            'propertyType' => 'Single Family Residential',
        ],
        'pricing' => [
            'acquisition' => 865000,
            'currentEstimate' => 914000,
            'potentialAfterGlow' => 982000,
        ],
        'last_updated' => now()->subHours(6)->toIso8601String(),
        'last_updated_human' => now()->subHours(6)->diffForHumans(),
        'tags' => ['Downtown Austin', 'High ROI', 'Owner-Occupied'],
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
                    'endpoint' => "/api/properties/{$propertyId}",
                    'status' => 'ready',
                    'last_run_at' => now()->subHours(3)->toIso8601String(),
                ],
                'pixrWorth' => [
                    'endpoint' => "/api/properties/{$propertyId}/worth",
                    'status' => 'needs-action',
                    'last_run_at' => now()->subDays(2)->toIso8601String(),
                ],
                'pixrGlowUp' => [
                    'endpoint' => "/api/properties/{$propertyId}/glowup/jobs",
                    'status' => 'ready',
                    'last_run_at' => now()->subHours(12)->toIso8601String(),
                ],
                'pixrSpyHunt' => [
                    'endpoint' => "/api/properties/{$propertyId}/spyhunt",
                    'status' => 'processing',
                    'last_run_at' => now()->subMinutes(45)->toIso8601String(),
                ],
                'pixrVision' => [
                    'endpoint' => "/api/properties/{$propertyId}/vision",
                    'status' => 'ready',
                    'last_run_at' => now()->subDays(5)->toIso8601String(),
                ],
                'pixrSeal' => [
                    'endpoint' => "/api/properties/{$propertyId}/report",
                    'status' => 'draft',
                    'last_run_at' => null,
                ],
                'pixrCollab' => [
                    'endpoint' => "/api/properties/{$propertyId}/collab/token",
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
