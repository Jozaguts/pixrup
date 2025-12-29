<?php

namespace App\Infrastructure\Auth\Providers;

use App\Application\Auth\Contracts\CurrentUserProvider;
use App\Models\User;

final class LaravelCurrentUserProvider implements CurrentUserProvider
{
    public function user(): ?User
    {
        $u = \Illuminate\Support\Facades\Auth::user();
        return $u instanceof User ? $u : null;
    }
}