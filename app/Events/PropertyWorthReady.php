<?php

namespace App\Events;

use App\Models\Property;
use App\Models\PropertyWorth;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PropertyWorthReady
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Property $property,
        public PropertyWorth $worth
    ) {}
}
