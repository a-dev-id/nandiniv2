<?php

namespace App\Models;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AffiliateBooking extends Model
{
    protected $fillable = [
        'affiliate_id',
        'synced_webhotelier_booking_id',
        'source_system',
        'external_booking_id',
        'external_booking_reference',
        'affiliate_code_snapshot',
        'check_in_date',
        'check_out_date',
        'stay_nights',
        'room_revenue_amount',
        'currency',
        'booking_status',
        'source_status',
        'manual_booking_status',
        'manual_status_reason',
        'manual_status_set_by',
        'manual_status_set_at',
        'commission_rate_snapshot',
        'estimated_commission_amount',
        'commission_state',
        'attribution_warning',
        'calculation_unavailable_reason',
        'synchronization_warning',
        'source_created_at',
        'source_updated_at',
        'last_synced_at',
        'data_fingerprint',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'stay_nights' => 'integer',
        'room_revenue_amount' => 'decimal:2',
        'booking_status' => AffiliateBookingStatus::class,
        'manual_booking_status' => AffiliateBookingStatus::class,
        'commission_rate_snapshot' => 'decimal:2',
        'estimated_commission_amount' => 'decimal:2',
        'commission_state' => AffiliateCommissionState::class,
        'source_created_at' => 'datetime',
        'source_updated_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'manual_status_set_at' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function syncedWebhotelierBooking(): BelongsTo
    {
        return $this->belongsTo(SyncedWebhotelierBooking::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(AffiliateBookingRoom::class);
    }

    public function manualStatusSetter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manual_status_set_by');
    }

    public function commissionItem(): HasOne
    {
        return $this->hasOne(AffiliateCommissionItem::class);
    }

    public function roomTypesLabel(): string
    {
        $types = $this->relationLoaded('rooms')
            ? $this->rooms->pluck('room_type_name')
            : $this->rooms()->pluck('room_type_name');

        return $types->filter()->unique()->sort()->implode(', ') ?: 'Room details unavailable';
    }

    public function effectiveBookingStatus(): AffiliateBookingStatus
    {
        return $this->manual_booking_status ?? $this->booking_status;
    }

    public function commissionStatusLabel(): string
    {
        if ($this->commission_state === AffiliateCommissionState::Ineligible) {
            return $this->effectiveBookingStatus()->isIneligible()
                ? $this->effectiveBookingStatus()->label()
                : AffiliateCommissionState::Ineligible->label();
        }

        return $this->commission_state->label();
    }
}
