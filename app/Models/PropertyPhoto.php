<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'path',
        'original_name',
        'size',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
