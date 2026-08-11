<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliatePayout;
use App\Models\Permission;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use App\Services\Affiliate\AffiliateNotificationService;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayAffiliateCommissionService
{
    public function __construct(
        private readonly AffiliatePayoutNumberGenerator $numbers,
        private readonly AffiliateAuditService $audit,
        private readonly AffiliateNotificationService $notifications,
    ) {}

    public function pay(AffiliateCommissionItem $item, User $actor, mixed $paymentDate, string $reference, ?string $note = null): AffiliatePayout
    {
        abort_unless($actor->hasPermissionTo(Permission::AFFILIATE_PAYOUT_MANAGE), 403);

        $reference = trim($reference);

        if ($reference === '') {
            throw ValidationException::withMessages(['payment_reference' => 'A payment reference is required.']);
        }

        return DB::transaction(function () use ($item, $actor, $paymentDate, $reference, $note): AffiliatePayout {
            $item = AffiliateCommissionItem::query()
                ->with(['affiliate.paymentProfile', 'payoutItem'])
                ->lockForUpdate()
                ->findOrFail($item->id);

            if ($item->status !== AffiliateCommissionItemStatus::Approved || $item->payoutItem) {
                throw new DomainException('Only a pending-payment commission can be marked paid.');
            }

            $profile = $item->affiliate->paymentProfile;

            if (! $profile?->is_complete) {
                throw ValidationException::withMessages(['payment_profile' => 'Complete the Affiliate payment profile before recording payment.']);
            }

            $amount = $item->payableAmount();

            if (bccomp($amount, '0.00', 2) <= 0) {
                throw ValidationException::withMessages(['commission' => 'The commission amount must be greater than zero.']);
            }

            $paidAt = CarbonImmutable::parse((string) ($paymentDate ?: now()), config('app.timezone'))->startOfDay();
            $payout = AffiliatePayout::query()->create([
                'payout_number' => $this->numbers->next(),
                'affiliate_id' => $item->affiliate_id,
                'currency' => $item->currency,
                'gross_commission_amount' => $amount,
                'adjustment_amount' => '0.00',
                'adjustment_reason' => filled($note) ? trim((string) $note) : null,
                'net_payout_amount' => $amount,
                'payment_method_snapshot' => $profile->payment_method->value,
                'payment_details_masked_snapshot' => $profile->maskedDetails(),
                'status' => AffiliatePayoutStatus::Paid,
                'due_at' => $paidAt,
                'prepared_at' => now(),
                'prepared_by' => $actor->id,
                'paid_at' => $paidAt,
                'paid_by' => $actor->id,
                'payment_reference' => $reference,
            ]);
            $payout->items()->create([
                'affiliate_commission_item_id' => $item->id,
                'amount' => $amount,
            ]);
            $item->update(['status' => AffiliateCommissionItemStatus::Paid]);

            $this->audit->record($item->affiliate, 'affiliate_commission.paid_directly', $actor, [
                'commission_item_id' => $item->id,
                'payout_id' => $payout->id,
                'payout_number' => $payout->payout_number,
                'currency' => $payout->currency,
                'amount' => $amount,
                'payment_reference' => $reference,
                'payment_date' => $paidAt->toDateString(),
                'note' => filled($note) ? trim((string) $note) : null,
            ], $payout);
            $this->notifications->afterCommitPayoutPaid($payout);

            return $payout->fresh();
        });
    }
}
