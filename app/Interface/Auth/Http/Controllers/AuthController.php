<?php

namespace App\Interface\Auth\Http\Controllers;

use App\Application\Auth\UseCases\RegisterUser;
use App\Application\Auth\UseCases\LoginUser;
use Illuminate\Http\Request;
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
            'password' => 'required|string|min:6|confirmed',
        ]);

        $useCase->execute($request->only('name', 'email', 'password'));

        return redirect()->route('dashboard');
    }

    public function login(Request $request, LoginUser $useCase): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $useCase->execute($request->only('email', 'password'));

        return redirect()->route('dashboard');
    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        auth()->logout();
        return redirect()->route('home');
    }
}
