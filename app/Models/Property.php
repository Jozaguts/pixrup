<?php

namespace App\Models;
use App\Domain\Properties\Entities\PropertyEntity;
use App\Models\Scopes\OwnProperties;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property mixed $id
 * @property mixed $latestWorth
 */
#[ScopedBy([OwnProperties::class])]
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
        'property_type',
        'bedrooms',
        'bathrooms',
        'square_footage',
        'user_id'
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

    public function glowupJobs(): HasMany
    {
        return $this->hasMany(GlowupJob::class);
    }
    public function toEntity(): PropertyEntity
    {
        return new PropertyEntity(
            $this->id,
            $this->title,
            $this->status,
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
            (float) $this->lat,
            (float)  $this->lng,
            $this->place_id,
            $this->metadata,
            $this->property_type,
            $this->bedrooms,
            $this->bathrooms,
            $this->square_footage,
        );
    }
}
