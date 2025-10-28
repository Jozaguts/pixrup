<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'lat',
        'lng',
        'place_id',
        'metadata',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'metadata' => 'array',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    public function worths(): HasMany
    {
        return $this->hasMany(PropertyWorth::class);
    }

    public function latestWorth(): HasOne
    {
        return $this->hasOne(PropertyWorth::class)->latestOfMany('fetched_at');
    }
}
