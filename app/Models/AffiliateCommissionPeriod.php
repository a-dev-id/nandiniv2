<?php

namespace App\Models;

use App\Enums\AffiliateCommissionPeriodStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateCommissionPeriod extends Model
{
    protected $fillable = [
        'period_year',
        'period_month',
        'period_start_date',
        'period_end_date',
        'status',
        'prepared_at',
        'prepared_by',
        'finalized_at',
        'finalized_by',
        'notes',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'status' => AffiliateCommissionPeriodStatus::class,
        'prepared_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(AffiliateCommissionItem::class, 'commission_period_id');
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function isFinalized(): bool
    {
        return $this->status === AffiliateCommissionPeriodStatus::Finalized;
    }

    public function label(): string
    {
        return $this->period_start_date->format('F Y');
    }
}
