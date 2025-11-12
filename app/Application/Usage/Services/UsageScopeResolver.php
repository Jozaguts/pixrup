<?php

namespace App\Application\Usage\Services;

use App\Domain\Usage\ValueObjects\UsageScope;
use App\Models\User;

class UsageScopeResolver
{
    public function resolve(User $user): UsageScope
    {
        return UsageScope::fromUser($user);
    }
}
