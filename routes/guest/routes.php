<?php

use App\Http\Controllers\FeaturesController;
use App\Interface\Auth\Http\Controllers\AuthController;
use App\Interface\Auth\Http\Controllers\SocialAuthController;
use App\Http\Controllers\BlogController;
use Laravel\Fortify\Features;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome/index', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.store');

Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::prefix('features')->group( static function() {
    Route::get('/', [FeaturesController::class ,'index'])->name('features.index');
    Route::get('{feature:slug}', [FeaturesController::class ,'show'])->name('features.show');
});