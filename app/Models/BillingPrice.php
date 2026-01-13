<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingPrice extends Model
{
    protected $table = 'billing_prices';

    protected $fillable = [
        'billing_product_id',
        'stripe_price_id',
        'stripe_product_id',
        'type',
        'currency',
        'unit_amount',
        'active',
        'interval',
        'interval_count',
        'trial_period_days',
        'usage_type',
        'billing_scheme',
        'tax_behavior',
        'stripe_created_at',
        'raw',
    ];

    protected $casts = [
        'active' => 'bool',
        'raw' => 'array',
        'stripe_created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(BillingProduct::class, 'billing_product_id');
    }
}
