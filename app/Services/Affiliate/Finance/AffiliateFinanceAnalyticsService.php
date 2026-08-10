<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionState;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliatePayout;
use App\Models\AffiliatePayoutMinimum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AffiliateFinanceAnalyticsService
{
    public function __construct(private readonly AffiliateCurrencyConverter $currencies) {}

    public function forAffiliate(Affiliate $affiliate): array
    {
        $preferredCurrency = $affiliate->paymentProfile?->preferred_currency?->value ?? 'IDR';

        return [
            'summary' => [
                'estimated' => $this->convertTotals($this->bookingTotals($affiliate, [AffiliateCommissionState::Estimated]), $preferredCurrency),
                'pending' => $this->convertTotals($this->pendingTotals($affiliate), $preferredCurrency),
                'paid' => $this->paidPayoutTotals($affiliate),
            ],
            'commissionHistory' => AffiliateBooking::query()
                ->where('affiliate_id', $affiliate->id)
                ->with(['rooms', 'commissionItem.payoutItem.payout'])
                ->orderByDesc('check_out_date')
                ->paginate(10, ['*'], 'commission_page')
                ->withQueryString(),
            'payoutHistory' => AffiliatePayout::query()
                ->where('affiliate_id', $affiliate->id)
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'payout_page')
                ->withQueryString(),
            'notices' => $this->notices($affiliate),
        ];
    }

    private function convertTotals(array $totals, string $preferredCurrency): array
    {
        $converted = collect($totals)->map(function (array $total) use ($preferredCurrency): array {
            $conversion = $this->currencies->convert($total['amount'], $total['currency'], $preferredCurrency, false);

            return $conversion ? [
                'currency' => $preferredCurrency,
                'amount' => $conversion['amount'],
                'estimated_conversion' => $total['currency'] !== $preferredCurrency,
                'original_currency' => $total['currency'],
                'original_amount' => $total['amount'],
            ] : [
                ...$total,
                'estimated_conversion' => false,
                'conversion_unavailable' => true,
            ];
        });

        return $converted->groupBy('currency')->map(function (Collection $rows, string $currency): array {
            return [
                'currency' => $currency,
                'amount' => app(DecimalMoney::class)->sum($rows->pluck('amount')),
                'estimated_conversion' => $rows->contains('estimated_conversion', true),
                'conversion_unavailable' => $rows->contains('conversion_unavailable', true),
            ];
        })->values()->all();
    }

    private function paidPayoutTotals(Affiliate $affiliate): array
    {
        return AffiliatePayout::query()
            ->where('affiliate_id', $affiliate->id)
            ->where('status', \App\Enums\AffiliatePayoutStatus::Paid)
            ->select('currency', DB::raw('SUM(net_payout_amount) amount'))
            ->groupBy('currency')
            ->orderBy('currency')
            ->get()
            ->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])
            ->all();
    }

    private function bookingTotals(Affiliate $affiliate, array $states): array
    {
        return AffiliateBooking::query()->where('affiliate_id', $affiliate->id)
            ->whereIn('commission_state', $states)
            ->whereNotNull('estimated_commission_amount')->whereNotNull('currency')
            ->select('currency', DB::raw('SUM(estimated_commission_amount) amount'))->groupBy('currency')->orderBy('currency')->get()
            ->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }

    private function pendingTotals(Affiliate $affiliate): array
    {
        $totals = collect($this->bookingTotalsWithoutItem($affiliate));
        $items = collect($this->itemTotals($affiliate, [
            AffiliateCommissionItemStatus::PendingReview,
            AffiliateCommissionItemStatus::Held,
            AffiliateCommissionItemStatus::Approved,
            AffiliateCommissionItemStatus::IncludedInPayout,
        ]));

        return $totals->concat($items)->groupBy('currency')->map(fn (Collection $rows, string $currency): array => [
            'currency' => $currency,
            'amount' => app(DecimalMoney::class)->sum($rows->pluck('amount')),
        ])->values()->all();
    }

    private function bookingTotalsWithoutItem(Affiliate $affiliate): array
    {
        return AffiliateBooking::query()->where('affiliate_id', $affiliate->id)
            ->where('commission_state', AffiliateCommissionState::PendingValidation)->whereDoesntHave('commissionItem')
            ->whereNotNull('estimated_commission_amount')->whereNotNull('currency')
            ->select('currency', DB::raw('SUM(estimated_commission_amount) amount'))->groupBy('currency')->get()
            ->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }

    private function itemTotals(Affiliate $affiliate, array $statuses): array
    {
        return AffiliateCommissionItem::query()->where('affiliate_id', $affiliate->id)->whereIn('status', $statuses)
            ->select('currency', DB::raw('SUM(COALESCE(approved_commission_amount, original_commission_amount)) amount'))
            ->groupBy('currency')->orderBy('currency')->get()
            ->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }

    private function notices(Affiliate $affiliate): array
    {
        $approved = collect($this->itemTotals($affiliate, [AffiliateCommissionItemStatus::Approved]));
        $notices = [];

        if ($approved->isNotEmpty() && ! $affiliate->paymentProfile?->is_complete) {
            $notices[] = ['type' => 'profile', 'message' => 'Add your payment details to receive eligible commission payouts.'];
        }

        foreach ($approved as $total) {
            $minimum = AffiliatePayoutMinimum::query()->where('currency', $total['currency'])->where('is_active', true)->first();

            if (! $minimum) {
                $notices[] = ['type' => 'threshold', 'currency' => $total['currency'], 'message' => 'Payout setup for this currency is being reviewed by Finance.'];
            } elseif (bccomp($total['amount'], $minimum->minimum_amount, 2) < 0) {
                $notices[] = ['type' => 'carry', 'currency' => $total['currency'], 'message' => 'Your approved commission will carry forward until it reaches the minimum payout amount for this currency.'];
            }
        }

        return $notices;
    }
}
