<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use App\Observers\UsagePropertyMonthlyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * @property User $user
 */
#[ObservedBy(UsagePropertyMonthlyObserver::class)]
class UsagePropertyMonthly extends Model
{
    use HasFactory;

    public string $period_key = '';
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
