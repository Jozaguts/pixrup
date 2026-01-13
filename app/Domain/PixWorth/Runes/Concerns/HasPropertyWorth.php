<?php

namespace App\Domain\PixWorth\Runes\Concerns;

use App\Models\PropertyWorth;

trait HasPropertyWorth
{
    public function __construct(protected PropertyWorth $worth)
    {
    }

    protected function propertyId(): int
    {
        return $this->worth->property_id;
    }

    protected function provider(): string
    {
        return 'pix_worth';
    }
}
