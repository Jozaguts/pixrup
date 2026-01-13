<?php

namespace App\Application\Usage\Contracts;

use App\Models\User;

interface UsageUserStore
{
    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function transaction(callable $callback): mixed;

    public function lock(User $user): User;

    public function save(User $user): void;
}
