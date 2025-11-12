<?php

namespace App\Domain\Usage\ValueObjects;

use App\Models\User;

final class UsageScope
{
    public function __construct(
        public readonly string $type,
        public readonly int $id,
        public readonly string $name,
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self('user', (int) $user->getKey(), $user->name ?? 'Account');
    }
}
