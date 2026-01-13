<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingProduct extends Model
{
    protected $table = 'billing_products';

    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'is_active',
        'sort_order',
        'stripe_product_id',
        'primary_price_id',
        'stripe_default_price_id',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(BillingPrice::class, 'billing_product_id');
    }

    public function primaryPrice(): ?BillingPrice
    {
        $stripePriceId = $this->primary_price_id ?: $this->stripe_default_price_id;
        if ($stripePriceId) {
            $price = $this->prices()
                ->where('stripe_price_id', $stripePriceId)
                ->first();
            if ($price) {
                return $price;
            }
        }

        return $this->prices()
            ->where('active', true)
            ->orderBy('unit_amount')
            ->first();
    }
}
