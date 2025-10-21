<?php

namespace App\Infrastructure\Auth\Persistence;

use App\Models\User;
use App\Domain\Auth\Entities\UserEntity;
use App\Domain\Auth\Repositories\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?UserEntity
    {
        $user = User::where('email', $email)->first();
        return $user ? $this->toEntity($user) : null;
    }

    public function findById(int $id): ?UserEntity
    {
        $user = User::find($id);
        return $user ? $this->toEntity($user) : null;
    }

    public function create(array $data): UserEntity
    {
        $user = User::create($data);
        return $this->toEntity($user);
    }

    public function update(int $id, array $data): UserEntity
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $this->toEntity($user);
    }

    private function toEntity(User $user): UserEntity
    {
        return new UserEntity(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            password: $user->password,
            email_verified_at: $user->email_verified_at,
            provider: $user->provider,
            provider_id: $user->provider_id,
            role: $user->role,
            created_at: $user->created_at,
            updated_at: $user->updated_at,
        );
    }
}
