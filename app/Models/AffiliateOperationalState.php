<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateOperationalState extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'status', 'summary', 'last_attempted_at', 'last_successful_at', 'metadata'];

    protected $casts = [
        'last_attempted_at' => 'datetime',
        'last_successful_at' => 'datetime',
        'metadata' => 'array',
    ];
}
