<?php
use App\Http\Controllers\Properties\PropertyController;
use App\Http\Controllers\Properties\PropertyWorthController;
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
    $properties = Property::with('latestWorth')
        ->latest()
        ->get()
        ->map(function (Property $property) {
            $status = in_array($property->status, ['in-progress', 'ready', 'pending', 'draft'], true)
                ? $property->status
                : 'in-progress';

            $addressSegments = collect([
                $property->address,
                collect([$property->city, $property->state])->filter()->implode(', '),
                $property->postal_code,
                $property->country,
            ])->filter();

            return [
                'id' => $property->id,
                'title' => $property->title ?? $property->address,
                'address' => $addressSegments->implode(', '),
                'status' => $status,
                'estimatedValue' => $property->latestWorth?->value,
                'progress' => null,
                'thumbnail' => null,
                'links' => [
                    'view' => route('properties.show', $property, absolute: false),
                    'report' => null,
                ],
            ];
        })
        ->values();

    return Inertia::render('Dashboard', [
        'properties' => $properties,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/properties/new', [PropertyController::class, 'create'])->name('properties.new');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::post('/properties/{property}/worth/fetch', [PropertyWorthController::class, 'fetch'])->name('properties.worth.fetch');
    Route::post('/properties/{property}/worth/report', [PropertyWorthController::class, 'report'])->name('properties.worth.report');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.store');

Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
require __DIR__.'/settings.php';
