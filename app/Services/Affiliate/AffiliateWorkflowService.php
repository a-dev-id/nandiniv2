<?php

namespace App\Services\Affiliate;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class AffiliateWorkflowService
{
    public function __construct(
        private readonly AffiliateAuditService $audit,
        private readonly AffiliateNotificationService $notifications,
    ) {}

    public function approve(Affiliate $affiliate, User $actor): Affiliate
    {
        return DB::transaction(function () use ($affiliate, $actor): Affiliate {
            $locked = Affiliate::query()->lockForUpdate()->findOrFail($affiliate->getKey());

            if (! $locked->isPending()) {
                throw new DomainException('Only a pending affiliate can be approved.');
            }

            if (blank($locked->password) || blank($locked->affiliate_code) || blank($locked->short_link_slug)) {
                throw new DomainException('The affiliate account is incomplete and cannot be approved.');
            }

            $approvedAt = now();
            $locked->update([
                'status' => AffiliateStatus::Approved,
                'approved_by' => $actor->getKey(),
                'approved_at' => $approvedAt,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'short_link_activated_at' => $approvedAt,
            ]);

            $this->audit->record($locked, 'approved', $actor, ['from' => AffiliateStatus::Pending->value, 'to' => AffiliateStatus::Approved->value]);
            $this->notifications->afterCommitApproval($locked);

            return $locked;
        }, 3);
    }

    public function reject(Affiliate $affiliate, User $actor, string $reason): Affiliate
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw new DomainException('A rejection reason is required.');
        }

        return DB::transaction(function () use ($affiliate, $actor, $reason): Affiliate {
            $locked = Affiliate::query()->lockForUpdate()->findOrFail($affiliate->getKey());

            if (! $locked->isPending()) {
                throw new DomainException('Only a pending affiliate can be rejected.');
            }

            if (blank($locked->password)) {
                throw new DomainException('The affiliate account is incomplete and cannot be rejected.');
            }

            $locked->update([
                'status' => AffiliateStatus::Rejected,
                'rejected_by' => $actor->getKey(),
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'approved_by' => null,
                'approved_at' => null,
                'short_link_activated_at' => null,
            ]);

            $this->audit->record($locked, 'rejected', $actor, ['from' => AffiliateStatus::Pending->value, 'to' => AffiliateStatus::Rejected->value]);
            $this->notifications->afterCommitRejection($locked);

            return $locked;
        }, 3);
    }
}
