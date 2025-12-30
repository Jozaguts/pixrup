<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsaMarketPulse extends Model
{
    protected $table = 'msa_market_pulses';

    protected $fillable = [
        'msa',
        'msa_name',
        'provider',
        'inventory_pressure',
        'supply_demand',
        'pricing_momentum',
        'payload',
        'fetched_at',
        'expires_at',
        'status',
        'error_code',
        'error_message',
    ];

    protected $casts = [
        'inventory_pressure' => 'float',
        'supply_demand' => 'float',
        'pricing_momentum' => 'float',
        'payload' => 'array',
        'fetched_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
