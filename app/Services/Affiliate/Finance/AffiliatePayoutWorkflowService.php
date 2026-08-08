<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Models\AffiliatePayout;
use App\Models\Permission;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use App\Services\Affiliate\AffiliateNotificationService;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AffiliatePayoutWorkflowService
{
    public function __construct(
        private readonly AffiliateAuditService $audit,
        private readonly DecimalMoney $money,
        private readonly AffiliateNotificationService $notifications,
    ) {}

    public function adjust(AffiliatePayout $payout, User $actor, mixed $adjustment, ?string $reason): AffiliatePayout
    {
        $this->authorize($actor);
        $adjustment = $this->money->normalize($adjustment, true, 'adjustment_amount');

        if (bccomp($adjustment, '0.00', 2) !== 0 && blank($reason)) {
            throw ValidationException::withMessages(['adjustment_reason' => 'An adjustment reason is required for a non-zero payout adjustment.']);
        }

        return DB::transaction(function () use ($payout, $actor, $adjustment, $reason): AffiliatePayout {
            $payout = AffiliatePayout::query()->with('affiliate')->lockForUpdate()->findOrFail($payout->id);
            $this->assertNotPaid($payout);
            $net = bcadd($payout->gross_commission_amount, $adjustment, 2);

            if (bccomp($net, '0.00', 2) <= 0) {
                throw ValidationException::withMessages(['adjustment_amount' => 'The final payout amount must be greater than zero.']);
            }

            $payout->update(['adjustment_amount' => $adjustment, 'adjustment_reason' => bccomp($adjustment, '0.00', 2) === 0 ? null : trim((string) $reason), 'net_payout_amount' => $net]);
            $this->auditEvent($payout, $actor, 'affiliate_payout.adjusted', [
                'adjustment_amount' => $adjustment,
                'adjustment_reason' => bccomp($adjustment, '0.00', 2) === 0 ? null : trim((string) $reason),
            ]);

            return $payout->fresh();
        });
    }

    public function markReady(AffiliatePayout $payout, User $actor): AffiliatePayout
    {
        return $this->transition($payout, $actor, [AffiliatePayoutStatus::Draft, AffiliatePayoutStatus::Failed], AffiliatePayoutStatus::Ready, 'affiliate_payout.ready');
    }

    public function startProcessing(AffiliatePayout $payout, User $actor): AffiliatePayout
    {
        return $this->transition($payout, $actor, [AffiliatePayoutStatus::Ready], AffiliatePayoutStatus::Processing, 'affiliate_payout.processing_started', ['processing_at' => now(), 'processing_by' => $actor->id]);
    }

    public function markPaid(AffiliatePayout $payout, User $actor, mixed $paymentDate, string $reference): AffiliatePayout
    {
        $reference = trim($reference);

        if ($reference === '') {
            throw ValidationException::withMessages(['payment_reference' => 'A payment reference is required.']);
        }

        return DB::transaction(function () use ($payout, $actor, $paymentDate, $reference): AffiliatePayout {
            $this->authorize($actor);
            $payout = AffiliatePayout::query()->with(['affiliate', 'items.commissionItem'])->lockForUpdate()->findOrFail($payout->id);

            if (! in_array($payout->status, [AffiliatePayoutStatus::Ready, AffiliatePayoutStatus::Processing], true)) {
                throw new DomainException('Only a ready or processing payout can be marked paid.');
            }

            $paidAt = CarbonImmutable::parse((string) ($paymentDate ?: now()), config('app.timezone'))->startOfDay();
            $payout->update(['status' => AffiliatePayoutStatus::Paid, 'paid_at' => $paidAt, 'paid_by' => $actor->id, 'payment_reference' => $reference]);
            foreach ($payout->items as $payoutItem) {
                $payoutItem->commissionItem->update(['status' => AffiliateCommissionItemStatus::Paid]);
            }
            $this->auditEvent($payout, $actor, 'affiliate_payout.paid');
            $this->notifications->afterCommitPayoutPaid($payout);

            return $payout->fresh();
        });
    }

    public function markFailed(AffiliatePayout $payout, User $actor, string $reason): AffiliatePayout
    {
        $reason = $this->reason($reason, 'failure_reason');

        return $this->transition($payout, $actor, [AffiliatePayoutStatus::Ready, AffiliatePayoutStatus::Processing], AffiliatePayoutStatus::Failed, 'affiliate_payout.failed', ['failure_reason' => $reason], ['failure_reason' => $reason]);
    }

    public function cancel(AffiliatePayout $payout, User $actor, string $reason): AffiliatePayout
    {
        $this->authorize($actor);
        $reason = $this->reason($reason, 'cancellation_reason');

        return DB::transaction(function () use ($payout, $actor, $reason): AffiliatePayout {
            $payout = AffiliatePayout::query()->with(['affiliate', 'items.commissionItem'])->lockForUpdate()->findOrFail($payout->id);
            $this->assertNotPaid($payout);

            if ($payout->status === AffiliatePayoutStatus::Cancelled) {
                throw new DomainException('This payout is already cancelled.');
            }

            foreach ($payout->items as $payoutItem) {
                $item = $payoutItem->commissionItem;
                $item->update(['status' => AffiliateCommissionItemStatus::Approved]);
                $this->audit->record($payout->affiliate, 'affiliate_commission.released_from_cancelled_payout', $actor, ['commission_item_id' => $item->id, 'payout_number' => $payout->payout_number], $item);
                $payoutItem->delete();
            }
            $payout->update(['status' => AffiliatePayoutStatus::Cancelled, 'cancelled_at' => now(), 'cancelled_by' => $actor->id, 'cancellation_reason' => $reason]);
            $this->auditEvent($payout, $actor, 'affiliate_payout.cancelled', ['cancellation_reason' => $reason]);

            return $payout->fresh();
        });
    }

    private function transition(AffiliatePayout $payout, User $actor, array $from, AffiliatePayoutStatus $to, string $event, array $extra = [], array $auditMetadata = []): AffiliatePayout
    {
        $this->authorize($actor);

        return DB::transaction(function () use ($payout, $actor, $from, $to, $event, $extra, $auditMetadata): AffiliatePayout {
            $payout = AffiliatePayout::query()->with('affiliate.paymentProfile')->lockForUpdate()->findOrFail($payout->id);

            if (! in_array($payout->status, $from, true)) {
                throw new DomainException('This payout transition is not allowed from its current status.');
            }

            if (bccomp($payout->net_payout_amount, '0.00', 2) <= 0 || $payout->items()->doesntExist()) {
                throw ValidationException::withMessages(['payout' => 'The payout must contain valid commission items and a positive net amount.']);
            }

            if (! $payout->affiliate->paymentProfile?->is_complete) {
                throw ValidationException::withMessages(['payout' => 'A complete Affiliate payment profile is required.']);
            }

            if ($payout->affiliate->status->value !== 'approved'
                || $payout->items()->whereHas('commissionItem', fn ($query) => $query->where('status', '!=', AffiliateCommissionItemStatus::IncludedInPayout))->exists()) {
                throw ValidationException::withMessages(['payout' => 'The Affiliate account and linked commission items require Finance review.']);
            }

            $payout->update(['status' => $to, ...$extra]);
            $this->auditEvent($payout, $actor, $event, $auditMetadata);

            return $payout->fresh();
        });
    }

    private function auditEvent(AffiliatePayout $payout, User $actor, string $event, array $extra = []): void
    {
        $this->audit->record($payout->affiliate, $event, $actor, [
            'payout_id' => $payout->id,
            'payout_number' => $payout->payout_number,
            'currency' => $payout->currency,
            'net_payout_amount' => $payout->net_payout_amount,
            'status' => $payout->status->value,
            ...$extra,
        ], $payout);
    }

    private function assertNotPaid(AffiliatePayout $payout): void
    {
        if ($payout->status === AffiliatePayoutStatus::Paid) {
            throw new DomainException('Paid payout history is immutable.');
        }
    }

    private function authorize(User $actor): void
    {
        if (! $actor->hasPermissionTo(Permission::AFFILIATE_PAYOUT_MANAGE)) {
            abort(403);
        }
    }

    private function reason(string $reason, string $field): string
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages([$field => 'A documented reason is required.']);
        }

        return trim($reason);
    }
}
