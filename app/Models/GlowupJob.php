<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlowupJob extends Model
{
    /** @use HasFactory<\Database\Factories\GlowupJobFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_ERROR = 'error';

    protected $fillable = [
        'property_id',
        'user_id',
        'room_type',
        'style',
        'before_url',
        'after_url',
        'status',
        'error_message',
        'meta',
        'usage_recorded_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'usage_recorded_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTerminal(): bool
    {
        return in_array(
            $this->status,
            [self::STATUS_DONE, self::STATUS_ERROR],
            true,
        );
    }
}
