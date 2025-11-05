<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyWorth extends Model
{
    use HasFactory;

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
