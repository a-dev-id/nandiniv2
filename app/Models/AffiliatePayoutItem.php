<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePayoutItem extends Model
{
    protected $fillable = ['affiliate_payout_id', 'affiliate_commission_item_id', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function payout(): BelongsTo
    {
        return $this->belongsTo(AffiliatePayout::class, 'affiliate_payout_id');
    }

    public function commissionItem(): BelongsTo
    {
        return $this->belongsTo(AffiliateCommissionItem::class, 'affiliate_commission_item_id');
    }
}
