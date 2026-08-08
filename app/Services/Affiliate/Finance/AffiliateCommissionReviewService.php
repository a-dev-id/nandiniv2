<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliateCommissionPeriod;
use App\Models\Permission;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AffiliateCommissionReviewService
{
    public function __construct(
        private readonly AffiliateAuditService $audit,
        private readonly DecimalMoney $money,
        private readonly PrepareAffiliatePayoutsService $payouts,
    ) {}

    public function approve(AffiliateCommissionItem $item, User $actor, mixed $amount, ?string $reason = null): AffiliateCommissionItem
    {
        $this->authorize($actor, Permission::AFFILIATE_COMMISSION_APPROVE);
        $approved = $this->money->normalize($amount, field: 'approved_commission_amount');
        $adjusted = bccomp($approved, $item->original_commission_amount, 2) !== 0;

        if ($adjusted && blank($reason)) {
            throw ValidationException::withMessages(['adjustment_reason' => 'An adjustment reason is required when the approved amount changes.']);
        }

        return DB::transaction(function () use ($item, $actor, $approved, $adjusted, $reason): AffiliateCommissionItem {
            $item = AffiliateCommissionItem::query()->with('period', 'affiliate')->lockForUpdate()->findOrFail($item->id);
            $this->assertReviewable($item);
            $item->update([
                'approved_commission_amount' => $approved,
                'adjustment_reason' => $adjusted ? trim((string) $reason) : null,
                'status' => AffiliateCommissionItemStatus::Approved,
                'hold_reason' => null,
                'exclusion_reason' => null,
                'source_changed_after_review' => false,
                'reviewed_at' => now(),
                'reviewed_by' => $actor->id,
                'approved_at' => now(),
                'approved_by' => $actor->id,
            ]);
            $this->audit->record($item->affiliate, $adjusted ? 'affiliate_commission.adjusted_and_approved' : 'affiliate_commission.approved', $actor, [
                'commission_item_id' => $item->id,
                'currency' => $item->currency,
                'original_commission_amount' => $item->original_commission_amount,
                'approved_commission_amount' => $approved,
                'adjusted' => $adjusted,
            ], $item);

            return $item->fresh();
        });
    }

    public function hold(AffiliateCommissionItem $item, User $actor, string $reason): AffiliateCommissionItem
    {
        $this->authorize($actor, Permission::AFFILIATE_COMMISSION_VALIDATE);
        $reason = $this->reason($reason, 'hold_reason');

        return $this->decision($item, $actor, AffiliateCommissionItemStatus::Held, 'hold_reason', $reason, 'affiliate_commission.held');
    }

    public function exclude(AffiliateCommissionItem $item, User $actor, string $reason): AffiliateCommissionItem
    {
        $this->authorize($actor, Permission::AFFILIATE_COMMISSION_VALIDATE);
        $reason = $this->reason($reason, 'exclusion_reason');

        return $this->decision($item, $actor, AffiliateCommissionItemStatus::Excluded, 'exclusion_reason', $reason, 'affiliate_commission.excluded');
    }

    public function finalize(AffiliateCommissionPeriod $period, User $actor): AffiliateCommissionPeriod
    {
        $this->authorize($actor, Permission::AFFILIATE_COMMISSION_VALIDATE);

        $period = DB::transaction(function () use ($period, $actor): AffiliateCommissionPeriod {
            $period = AffiliateCommissionPeriod::query()->lockForUpdate()->findOrFail($period->id);

            if ($period->isFinalized()) {
                throw new DomainException('This commission period is already finalized.');
            }

            if ($period->items()->where('status', AffiliateCommissionItemStatus::PendingReview)->exists()) {
                throw ValidationException::withMessages(['period' => 'Review every pending commission item before finalizing this period.']);
            }

            $period->update([
                'status' => AffiliateCommissionPeriodStatus::Finalized,
                'finalized_at' => now(),
                'finalized_by' => $actor->id,
            ]);
            $this->audit->record(null, 'affiliate_commission_period.finalized', $actor, [
                'period_year' => $period->period_year,
                'period_month' => $period->period_month,
            ], $period);

            return $period->fresh();
        });

        $this->payouts->prepare($actor);

        return $period;
    }

    public function reopen(AffiliateCommissionPeriod $period, User $actor, string $reason): AffiliateCommissionPeriod
    {
        $this->authorize($actor, Permission::AFFILIATE_COMMISSION_APPROVE);
        $reason = $this->reason($reason, 'reason');

        return DB::transaction(function () use ($period, $actor, $reason): AffiliateCommissionPeriod {
            $period = AffiliateCommissionPeriod::query()->lockForUpdate()->findOrFail($period->id);

            if (! $period->isFinalized()) {
                throw new DomainException('Only a finalized commission period can be reopened.');
            }

            if ($period->items()->where('status', AffiliateCommissionItemStatus::Paid)->exists()) {
                throw ValidationException::withMessages(['period' => 'A period containing paid commission cannot be reopened. Use a future correction workflow.']);
            }

            $period->update([
                'status' => AffiliateCommissionPeriodStatus::Reopened,
                'finalized_at' => null,
                'finalized_by' => null,
                'notes' => trim(($period->notes ? $period->notes."\n" : '').'Reopened: '.$reason),
            ]);
            $this->audit->record(null, 'affiliate_commission_period.reopened', $actor, [
                'period_year' => $period->period_year,
                'period_month' => $period->period_month,
                'reason' => $reason,
            ], $period);

            return $period->fresh();
        });
    }

    private function decision(AffiliateCommissionItem $item, User $actor, AffiliateCommissionItemStatus $status, string $reasonField, string $reason, string $event): AffiliateCommissionItem
    {
        return DB::transaction(function () use ($item, $actor, $status, $reasonField, $reason, $event): AffiliateCommissionItem {
            $item = AffiliateCommissionItem::query()->with('period', 'affiliate')->lockForUpdate()->findOrFail($item->id);
            $this->assertReviewable($item);
            $item->update([
                'status' => $status,
                $reasonField => $reason,
                'approved_commission_amount' => null,
                'adjustment_reason' => null,
                'approved_at' => null,
                'approved_by' => null,
                'reviewed_at' => now(),
                'reviewed_by' => $actor->id,
            ]);
            $this->audit->record($item->affiliate, $event, $actor, [
                'commission_item_id' => $item->id,
                'reason' => $reason,
            ], $item);

            return $item->fresh();
        });
    }

    private function assertReviewable(AffiliateCommissionItem $item): void
    {
        if ($item->period->isFinalized() || $item->status->isFinanciallyLocked()) {
            throw new DomainException('This commission item is financially locked.');
        }

        if ($item->status === AffiliateCommissionItemStatus::Excluded) {
            throw new DomainException('An excluded commission item must be reopened through a period review.');
        }
    }

    private function authorize(User $actor, string $permission): void
    {
        if (! $actor->hasPermissionTo($permission)) {
            abort(403);
        }
    }

    private function reason(string $reason, string $field): string
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages([$field => 'A documented reason is required.']);
        }

        return $reason;
    }
}
