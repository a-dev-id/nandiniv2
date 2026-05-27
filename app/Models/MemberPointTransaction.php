<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPointTransaction extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'points',
        'description',
        'reference_type',
        'reference_id',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function isEarn(): bool
    {
        return $this->type === 'earn';
    }

    public function isRedeem(): bool
    {
        return $this->type === 'redeem';
    }

    public function isAdjustment(): bool
    {
        return $this->type === 'adjustment';
    }
}
