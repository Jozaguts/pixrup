<?php

namespace App\Models;

use App\Observers\PropertyWorthObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed|null $comparables
 * @property mixed $property_id
 * @property float|null $value
 * @property float|null $value_low
 * @property float|null $value_high
 */
#[ObservedBy(PropertyWorthObserver::class)]
class PropertyWorth extends Model
{

    protected $fillable = [
        'property_id',
        'value',
        'value_low',
        'value_high',
        'confidence',
        'comparables',
        'trend',
        'provider',
        'fetched_at',
    ];

    protected $casts = [
        'value' => 'float',
        'value_low' => 'float',
        'value_high' => 'float',
        'confidence' => 'float',
        'comparables' => 'array',
        'trend' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
