<?php

namespace App\Application\Auth\Contracts;

use App\Models\User;

interface CurrentUserProvider
{
    public function user(): ?User;
}