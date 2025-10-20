<?php

namespace App\Domain\Auth\Entities;

use DateTimeInterface;
class UserEntity
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?string $provider = null,
        public ?string $provider_id = null,
        public string $role = 'user',
        public ?DateTimeInterface $created_at = null,
        public ?DateTimeInterface $updated_at = null,
    ) {}
}