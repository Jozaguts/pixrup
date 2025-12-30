<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyOverview extends Model
{
    protected $table = 'property_overviews';

    protected $fillable = [
        'property_id',
        'provider',
        'status',
        'owner_occupied',
        'fema_disaster_area',
        'flood_zone',
        'flood_risk',
        'crime_percentile',
        'crime_compare_scope',
        'msa',
        'msa_name',
        'census_tract',
        'block_group',
        'payload',
        'fetched_at',
        'expires_at',
        'error_code',
        'error_message',
    ];

    protected $casts = [
        'owner_occupied' => 'bool',
        'fema_disaster_area' => 'bool',
        'crime_percentile' => 'int',
        'payload' => 'array',
        'fetched_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
