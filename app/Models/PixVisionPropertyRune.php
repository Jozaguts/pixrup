<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create($toArray)
 * @method static upsert(array[] $array, string[] $array1, $param, string[] $array2)
 * @property mixed $rune_value
 * @property string $rune_key
 * @property string $provider
 */
class PixVisionPropertyRune extends Model
{
    use HasUuids;

    protected $table = 'pixvision_property_runes';

    protected $fillable = [
        'property_id',
        'provider',
        'rune_key',
        'rune_value',
        'version',
        'confidence',
        'computed_at',
    ];

    protected $casts = [
        'rune_value' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
