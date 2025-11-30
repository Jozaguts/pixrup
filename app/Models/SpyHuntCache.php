<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpyHuntCache extends Model
{
    protected $table = 'spy_hunt_cache';

    protected $fillable = [
        'property_id',
        'payload',
        'source',
        'last_synced_at',
        'expires_at',
    ];
    protected $casts = [
        'payload' => 'array',
        'last_synced_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

}
