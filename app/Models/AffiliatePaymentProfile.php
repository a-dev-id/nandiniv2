<?php

namespace App\Models;

use App\Enums\AffiliatePaymentMethod;
use App\Enums\AffiliatePreferredCurrency;
use App\Services\Affiliate\Finance\PaymentDetailMasker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePaymentProfile extends Model
{
    protected $fillable = [
        'affiliate_id',
        'payment_method',
        'preferred_currency',
        'account_holder_name',
        'wise_email',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_country',
        'swift_bic',
        'is_complete',
        'verified_at',
        'verified_by',
    ];

    protected $hidden = [
        'account_holder_name',
        'wise_email',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_country',
        'swift_bic',
    ];

    protected $casts = [
        'payment_method' => AffiliatePaymentMethod::class,
        'preferred_currency' => AffiliatePreferredCurrency::class,
        'account_holder_name' => 'encrypted',
        'wise_email' => 'encrypted',
        'bank_name' => 'encrypted',
        'bank_account_name' => 'encrypted',
        'bank_account_number' => 'encrypted',
        'bank_country' => 'encrypted',
        'swift_bic' => 'encrypted',
        'is_complete' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function maskedDetails(): string
    {
        return app(PaymentDetailMasker::class)->profile($this);
    }
}
