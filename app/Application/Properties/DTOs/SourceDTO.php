<?php

namespace App\Application\Properties\DTOs;

class SourceDTO
{
    public function __construct(
        public string $source,  // 'avm' | 'mls'
        public mixed $lastSync
    ) {
    }
    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'last_sync' => $this->lastSync,
        ];
    }

}