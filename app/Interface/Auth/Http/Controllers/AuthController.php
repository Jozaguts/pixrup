<?php

namespace App\Interface\Auth\Http\Controllers;

use App\Application\Auth\UseCases\LoginUser;
use App\Application\Auth\UseCases\RegisterUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController
{
    public function showLogin(): \Inertia\Response
    {
        return Inertia::render('auth/Login');
    }

    public function showRegister(): \Inertia\Response
    {
        return Inertia::render('auth/Register');
    }

    public function register(Request $request, RegisterUser $useCase): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ]);

        $userEntity = $useCase->execute($request->only('name', 'email', 'password'));

        /** @var \App\Models\User $user */
        $user = User::findOrFail($userEntity->id);

        event(new Registered($user));

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->route('verification.notice')->with('status', 'must-verify-email');
    }

    public function login(Request $request, LoginUser $useCase): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $this->ensureIsNotRateLimited($request);

        try {
            $result = $useCase->execute($request->only('email', 'password', 'remember'));
        } catch (ValidationException $exception) {
            RateLimiter::hit($this->throttleKey($request));

            throw $exception;
        }

        RateLimiter::clear($this->throttleKey($request));

        if ($result === 'two-factor') {
            return redirect()->route('two-factor.login');
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return Auth::user()?->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : redirect()->route('verification.notice')->with('status', 'must-verify-email');
    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        auth()->logout();
        return redirect()->route('home');
    }

    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', ['seconds' => $seconds]),
        ])->status(429);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}
