<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserImages;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

/**
 * @method static create(array $array)
 * @method static orderBy(string $string, string $string1)
 */
#[ScopedBy([UserImages::class])]
class Image extends Model
{
    protected $fillable = [
        'name',
        'uri',
        'user_uuid',
        'size',
        'type',
        'mime_type',
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($image) {
           $image->user_uuid = auth()->user()?->uuid;
        });
    }

}
