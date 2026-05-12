<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhotelierWebhookLog extends Model
{
    protected $fillable = [
        'source',
        'event_type',
        'property_code',
        'reservation_id',
        'confirmation_code',
        'booking_status',
        'method',
        'ip_address',
        'headers',
        'raw_body',
        'payload',
        'processing_status',
        'processing_error',
        'processed_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
