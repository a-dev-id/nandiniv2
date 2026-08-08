<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateAuditEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['affiliate_id', 'actor_user_id', 'actor_affiliate_id', 'event', 'subject_type', 'subject_id', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function affiliateActor(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class, 'actor_affiliate_id');
    }
}
