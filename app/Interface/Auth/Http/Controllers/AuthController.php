<?php

namespace App\Interface\Auth\Http\Controllers;

use App\Application\Auth\UseCases\LoginUser;
use App\Application\Auth\UseCases\RegisterUser;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
        ]);

        $useCase->execute($request->only('email', 'password'));

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
}
