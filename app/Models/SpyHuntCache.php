<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpyHuntCache extends Model
{
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

}
