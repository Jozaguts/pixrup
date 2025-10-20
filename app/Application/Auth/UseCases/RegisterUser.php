<?php

namespace App\Application\Auth\UseCases;

use App\Domain\Auth\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Domain\Auth\Entities\UserEntity;

class RegisterUser
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function execute(array $data): UserEntity
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'user';

        return $this->repository->create($data);
    }
}
