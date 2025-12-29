<?php

namespace App\Application\Properties\Overview\DTOs;

class OverviewDTO
{
    public function __construct(public array $array = [])
    {}

    public function toArray(): array
    {
        return [];
    }
    public static function fromArray(array $array): self
    {
        return  new self($array);
    }
}