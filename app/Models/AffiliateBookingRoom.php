<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateBookingRoom extends Model
{
    protected $fillable = [
        'affiliate_booking_id',
        'external_room_id',
        'room_type_name',
        'room_quantity',
        'stay_nights',
        'room_revenue_amount',
        'currency',
        'line_fingerprint',
    ];

    protected $casts = [
        'room_quantity' => 'integer',
        'stay_nights' => 'integer',
        'room_revenue_amount' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(AffiliateBooking::class, 'affiliate_booking_id');
    }
}
