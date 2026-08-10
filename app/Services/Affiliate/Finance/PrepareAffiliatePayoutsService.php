<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Enums\AffiliateStatus;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliatePayout;
use App\Models\AffiliatePayoutMinimum;
use App\Models\AffiliateProgramSetting;
use App\Models\User;
use App\Services\Affiliate\AffiliateAuditService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PrepareAffiliatePayoutsService
{
    public function __construct(
        private readonly DecimalMoney $money,
        private readonly AffiliatePayoutNumberGenerator $numbers,
        private readonly AffiliateAuditService $audit,
        private readonly AffiliateCurrencyConverter $currencies,
    ) {}

    /** @return array{created: int, carried: int, missing_profile: int, missing_threshold: int, account_review: int} */
    public function prepare(?User $actor = null): array
    {
        $summary = ['created' => 0, 'carried' => 0, 'missing_profile' => 0, 'missing_threshold' => 0, 'account_review' => 0];
        $items = AffiliateCommissionItem::query()
            ->with(['affiliate.paymentProfile', 'period'])
            ->where('status', AffiliateCommissionItemStatus::Approved)
            ->whereHas('period', fn ($query) => $query->where('status', AffiliateCommissionPeriodStatus::Finalized))
            ->whereDoesntHave('payoutItem')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (AffiliateCommissionItem $item): string => $item->affiliate_id.'|'.$item->currency);

        foreach ($items as $group) {
            $this->prepareGroup($group, $actor, $summary);
        }

        return $summary;
    }

    /** @param Collection<int, AffiliateCommissionItem> $items
     * @param  array{created: int, carried: int, missing_profile: int, missing_threshold: int, account_review: int}  $summary
     */
    private function prepareGroup(Collection $items, ?User $actor, array &$summary): void
    {
        $first = $items->first();
        $affiliate = $first->affiliate;

        if ($affiliate->status !== AffiliateStatus::Approved) {
            $summary['account_review'] += $items->count();

            return;
        }

        $threshold = AffiliatePayoutMinimum::query()->where('currency', $first->currency)->where('is_active', true)->first();

        if (! $threshold) {
            $summary['missing_threshold'] += $items->count();

            return;
        }

        $profile = $affiliate->paymentProfile;

        if (! $profile?->is_complete) {
            $summary['missing_profile'] += $items->count();

            return;
        }

        $total = $this->money->sum($items->pluck('approved_commission_amount'));

        if (bccomp($total, $threshold->minimum_amount, 2) < 0) {
            $summary['carried'] += $items->count();

            return;
        }

        DB::transaction(function () use ($items, $affiliate, $profile, $first, $total, $actor, &$summary): void {
            $locked = AffiliateCommissionItem::query()
                ->whereKey($items->pluck('id'))
                ->where('status', AffiliateCommissionItemStatus::Approved)
                ->whereDoesntHave('payoutItem')
                ->lockForUpdate()
                ->get();

            if ($locked->count() !== $items->count()) {
                return;
            }

            $payoutCurrency = $profile->preferred_currency->value;
            $conversion = $this->currencies->convert($total, $first->currency, $payoutCurrency);

            $payout = AffiliatePayout::query()->create([
                'payout_number' => $this->numbers->next(),
                'affiliate_id' => $affiliate->id,
                'currency' => $payoutCurrency,
                'source_currency' => $first->currency,
                'source_amount' => $total,
                'exchange_rate_snapshot' => $conversion['rate'],
                'gross_commission_amount' => $conversion['amount'],
                'adjustment_amount' => '0.00',
                'net_payout_amount' => $conversion['amount'],
                'payment_method_snapshot' => $profile->payment_method->value,
                'payment_details_masked_snapshot' => $profile->maskedDetails(),
                'status' => AffiliatePayoutStatus::Draft,
                'due_at' => now()->addDays(AffiliateProgramSetting::current()->payout_release_days),
                'prepared_at' => now(),
                'prepared_by' => $actor?->id,
            ]);

            foreach ($locked as $item) {
                $payout->items()->create([
                    'affiliate_commission_item_id' => $item->id,
                    'amount' => $item->approved_commission_amount,
                ]);
                $item->update(['status' => AffiliateCommissionItemStatus::IncludedInPayout]);
            }

            $this->audit->record($affiliate, 'affiliate_payout.created', $actor, [
                'payout_id' => $payout->id,
                'payout_number' => $payout->payout_number,
                'currency' => $payout->currency,
                'net_payout_amount' => $payout->net_payout_amount,
                'commission_item_count' => $locked->count(),
                'payment_method' => $payout->payment_method_snapshot,
                'payment_details' => $payout->payment_details_masked_snapshot,
            ], $payout);
            $summary['created']++;
        });
    }
}
