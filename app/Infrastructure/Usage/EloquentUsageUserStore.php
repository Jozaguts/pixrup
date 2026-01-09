<?php

namespace App\Infrastructure\Usage;

use App\Application\Usage\Contracts\UsageUserStore;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class EloquentUsageUserStore implements UsageUserStore
{
    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }

    public function lock(User $user): User
    {
        return User::query()
            ->whereKey($user->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function save(User $user): void
    {
        $user->save();
    }
}
