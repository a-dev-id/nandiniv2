<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSyncLog extends Model
{
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'started_at',
        'finished_at',
        'status',
        'bookings_received',
        'bookings_created',
        'bookings_updated',
        'members_created',
        'members_updated',
        'message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'bookings_received' => 'integer',
        'bookings_created' => 'integer',
        'bookings_updated' => 'integer',
        'members_created' => 'integer',
        'members_updated' => 'integer',
    ];
}
