<?php

namespace App\Domain\PixHunt\Concerns;

use App\Models\SpyHuntCache;

trait HasSpyHuntCache
{
    public function __construct(protected SpyHuntCache $cache)
    {
    }

    protected function propertyId(): int
    {
        return $this->cache->property_id;
    }

    protected function runeProvider(): string
    {
        return 'pix_hunt';
    }
}
