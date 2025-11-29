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
    public static function fromArray($array):self
    {
        return new self(
            $array['source'],
            $array['last_sync']
        );
    }

}