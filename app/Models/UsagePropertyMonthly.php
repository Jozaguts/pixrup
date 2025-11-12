<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsagePropertyMonthly extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_scope_type',
        'account_scope_id',
        'user_id',
        'property_id',
        'period_key',
        'plan_snapshot',
        'action_first',
        'first_action_at',
    ];

    protected $casts = [
        'first_action_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
