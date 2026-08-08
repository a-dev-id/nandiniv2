<?php

namespace App\Models;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Concerns\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Affiliate extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_whatsapp',
        'instagram',
        'facebook',
        'tiktok',
        'x',
        'threads',
        'status',
        'registration_source',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'suspended_by',
        'suspended_at',
        'status_note',
        'rejection_reason',
        'affiliate_code',
        'affiliate_code_generated_at',
        'short_link_slug',
        'short_link_activated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => AffiliateStatus::class,
        'registration_source' => AffiliateRegistrationSource::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'suspended_at' => 'datetime',
        'affiliate_code_generated_at' => 'datetime',
        'short_link_activated_at' => 'datetime',
        'dashboard_welcome_dismissed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function suspender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function auditEvents(): HasMany
    {
        return $this->hasMany(AffiliateAuditEvent::class);
    }

    public function clickEvents(): HasMany
    {
        return $this->hasMany(AffiliateClickEvent::class);
    }

    public function uniqueClicks(): HasMany
    {
        return $this->hasMany(AffiliateUniqueClick::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(AffiliateBooking::class);
    }

    public function commissionItems(): HasMany
    {
        return $this->hasMany(AffiliateCommissionItem::class);
    }

    public function paymentProfile(): HasOne
    {
        return $this->hasOne(AffiliatePaymentProfile::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    public function isPending(): bool
    {
        return $this->status === AffiliateStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === AffiliateStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === AffiliateStatus::Rejected;
    }

    public function isSuspended(): bool
    {
        return $this->status === AffiliateStatus::Suspended;
    }
}
