<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliatePayoutMinimum extends Model
{
    protected $fillable = ['currency', 'minimum_amount', 'is_active'];

    protected $casts = [
        'minimum_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
