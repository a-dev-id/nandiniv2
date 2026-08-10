<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SyncedWebhotelierBooking extends Model
{
    protected $fillable = [
        'member_id',
        'member_assigned_manually',
        'booking_number',
        'guest_name',
        'email',
        'phone',
        'check_in',
        'check_out',
        'rooms',
        'room_type',
        'room_name',
        'rate_name',
        'currency',
        'room_subtotal',
        'booking_total',
        'status',
        'source_name',
        'affiliate_code',
        'remote_updated_at',
        'last_synced_at',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'rooms' => 'integer',
        'member_assigned_manually' => 'boolean',
        'room_subtotal' => 'decimal:2',
        'booking_total' => 'decimal:2',
        'remote_updated_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function affiliateBooking(): HasOne
    {
        return $this->hasOne(AffiliateBooking::class);
    }
}
