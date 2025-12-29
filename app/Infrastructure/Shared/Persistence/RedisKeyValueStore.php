<?php

namespace App\Infrastructure\Shared\Persistence;

use App\Application\Shared\Contracts\Cache\KeyValueStore;
use Illuminate\Redis\RedisManager;

class RedisKeyValueStore implements KeyValueStore
{
    public function __construct(public RedisManager $redis){}
    public function get(string $key): ?string
    {
        $raw = $this->redis->get($key);
        return $raw === null ? null : (string) $raw;
    }

    public function put(string $key, string $value, int $ttlSeconds): void
    {
        $this->redis->setex($key, $ttlSeconds, $value);
    }

    public function forget(string $key): void
    {
        $this->redis->del($key);
    }
}