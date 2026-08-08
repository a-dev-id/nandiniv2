<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliatePaymentMethod;
use App\Enums\AffiliatePreferredCurrency;
use App\Models\Affiliate;
use App\Models\AffiliatePaymentProfile;
use App\Models\Permission;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use Illuminate\Support\Facades\DB;

class AffiliatePaymentProfileService
{
    public function __construct(private readonly AffiliateAuditService $audit) {}

    public function updateOwn(Affiliate $affiliate, array $data): AffiliatePaymentProfile
    {
        return DB::transaction(function () use ($affiliate, $data): AffiliatePaymentProfile {
            $method = AffiliatePaymentMethod::from($data['payment_method']);
            $attributes = [
                'payment_method' => $method,
                'preferred_currency' => $data['preferred_currency'] ?? AffiliatePreferredCurrency::IDR->value,
                'account_holder_name' => trim($data['account_holder_name']),
                'wise_email' => $method === AffiliatePaymentMethod::Wise ? mb_strtolower(trim($data['wise_email'])) : null,
                'bank_name' => $method === AffiliatePaymentMethod::BankTransfer ? trim($data['bank_name']) : null,
                'bank_account_name' => $method === AffiliatePaymentMethod::BankTransfer ? trim($data['bank_account_name']) : null,
                'bank_account_number' => $method === AffiliatePaymentMethod::BankTransfer ? preg_replace('/\s+/', '', $data['bank_account_number']) : null,
                'bank_country' => $method === AffiliatePaymentMethod::BankTransfer ? trim($data['bank_country']) : null,
                'swift_bic' => $method === AffiliatePaymentMethod::BankTransfer && filled($data['swift_bic'] ?? null) ? mb_strtoupper(trim($data['swift_bic'])) : null,
                'is_complete' => true,
                'verified_at' => now(),
                'verified_by' => null,
            ];
            $profile = $affiliate->paymentProfile()->updateOrCreate([], $attributes);
            $this->audit->record($affiliate, $profile->wasRecentlyCreated ? 'affiliate_payment_profile.created' : 'affiliate_payment_profile.updated', $affiliate, [
                'payment_method' => $method->value,
                'preferred_currency' => $data['preferred_currency'] ?? AffiliatePreferredCurrency::IDR->value,
                'is_complete' => true,
                'approval' => 'automatic',
            ], $profile);

            return $profile->fresh();
        });
    }

    public function markReviewed(AffiliatePaymentProfile $profile, User $actor): AffiliatePaymentProfile
    {
        $this->authorize($actor);

        return DB::transaction(function () use ($profile, $actor): AffiliatePaymentProfile {
            $profile->update(['is_complete' => true, 'verified_at' => now(), 'verified_by' => $actor->id]);
            $this->audit->record($profile->affiliate, 'affiliate_payment_profile.reviewed', $actor, [
                'payment_method' => $profile->payment_method->value,
                'is_complete' => true,
            ], $profile);

            return $profile->fresh();
        });
    }

    public function markIncomplete(AffiliatePaymentProfile $profile, User $actor): AffiliatePaymentProfile
    {
        $this->authorize($actor);

        return DB::transaction(function () use ($profile, $actor): AffiliatePaymentProfile {
            $profile->update(['is_complete' => false, 'verified_at' => null, 'verified_by' => null]);
            $this->audit->record($profile->affiliate, 'affiliate_payment_profile.marked_incomplete', $actor, [
                'payment_method' => $profile->payment_method->value,
                'is_complete' => false,
            ], $profile);

            return $profile->fresh();
        });
    }

    private function authorize(User $actor): void
    {
        if (! $actor->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_MANAGE)) {
            abort(403);
        }
    }
}
