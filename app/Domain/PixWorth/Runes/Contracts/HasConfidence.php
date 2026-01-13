<?php

namespace App\Domain\PixWorth\Runes\Contracts;

interface HasConfidence
{
    public function confidence(): float;
}
