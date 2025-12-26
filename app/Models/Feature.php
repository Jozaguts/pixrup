<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feature extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'icon',
        'is_active',
        'sort_order',
        'content'
    ];

    protected $casts = [
        'content' => 'array',
    ];

    protected static function booted()
    {
        static::saving(static function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }
}
