<?php

namespace App\Application\Shared\Contracts\Cache;

interface KeyValueStore
{
    public function get(string $key): ?string;
    public function put(string $key, string $value, int $ttlSeconds): void;
    public function forget(string $key): void;
}