<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhotelierReservation extends Model
{
    protected $fillable = [
        'webhotelier_id',
        'property_code',
        'event_type',
        'status_code',
        'status',
        'offline',
        'channelstream',
        'guest_email',
        'guest_first_name',
        'guest_last_name',
        'guest_phone',
        'checkin_date',
        'checkout_date',
        'rooms',
        'room_type',
        'room_name',
        'rate_name',
        'currency',
        'room_subtotal',
        'booking_total',
        'extras_total',
        'taxes_total',
        'source_id',
        'source_name',
        'last_webhook_log_id',
        'payload',
        'last_received_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'offline' => 'boolean',
        'channelstream' => 'boolean',
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'rooms' => 'integer',
        'room_subtotal' => 'decimal:2',
        'booking_total' => 'decimal:2',
        'extras_total' => 'decimal:2',
        'taxes_total' => 'decimal:2',
        'payload' => 'array',
        'last_received_at' => 'datetime',
    ];
}
