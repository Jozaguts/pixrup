<?php

namespace App\Models;

use App\Observers\PixHuntCacheObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $property_id
 */

#[ObservedBy(PixHuntCacheObserver::class)]
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
