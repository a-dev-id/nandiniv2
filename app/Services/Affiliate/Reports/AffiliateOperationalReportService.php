<?php

namespace App\Services\Affiliate\Reports;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateBookingRoom;
use App\Models\AffiliateClickEvent;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliatePayout;
use App\Models\BookingSyncLog;
use App\Models\SyncedWebhotelierBooking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AffiliateOperationalReportService
{
    public function dashboard(
        AffiliateReportDateRange $range,
        ?string $status = null,
        ?string $currency = null,
        ?int $affiliateId = null,
        ?string $registrationSource = null,
        ?int $approverId = null,
        ?string $commissionStatus = null,
        ?string $payoutStatus = null,
        ?int $reviewerId = null,
    ): array {
        $affiliates = Affiliate::query()
            ->when($status, fn (Builder $query): Builder => $query->where('status', $status))
            ->when($affiliateId, fn (Builder $query): Builder => $query->whereKey($affiliateId))
            ->when($registrationSource, fn (Builder $query): Builder => $query->where('registration_source', $registrationSource));
        $registered = Affiliate::query()->whereBetween('created_at', [$range->from, $range->to])
            ->when($status, fn (Builder $query): Builder => $query->where('status', $status))
            ->when($registrationSource, fn (Builder $query): Builder => $query->where('registration_source', $registrationSource))
            ->when($approverId, fn (Builder $query): Builder => $query->where('approved_by', $approverId));
        $performance = (clone $affiliates)->orderBy('name')->limit(100)->get()->map(function (Affiliate $affiliate) use ($range, $currency): array {
            $clicks = AffiliateClickEvent::query()->where('affiliate_id', $affiliate->id)->where('is_bot', false)->whereBetween('clicked_at', [$range->from, $range->to]);
            $bookings = AffiliateBooking::query()->where('affiliate_id', $affiliate->id)
                ->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()])
                ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency));
            $items = AffiliateCommissionItem::query()->where('affiliate_id', $affiliate->id)->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency))->whereHas('booking', fn (Builder $query): Builder => $query->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()]));
            $totalClicks = (clone $clicks)->count();
            $bookingCount = (clone $bookings)->count();

            return [
                'name' => $affiliate->name, 'code' => $affiliate->affiliate_code, 'status' => $affiliate->status->label(),
                'clicks' => $totalClicks, 'unique' => (clone $clicks)->where('is_unique', true)->count(),
                'bookings' => $bookingCount,
                'room_nights' => (int) AffiliateBookingRoom::query()->whereHas('booking', fn (Builder $query): Builder => $query
                    ->where('affiliate_id', $affiliate->id)
                    ->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()])
                    ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency)))
                    ->sum(DB::raw('stay_nights * room_quantity')),
                'estimated' => $this->moneyTotals(clone $bookings, 'estimated_commission_amount'),
                'approved' => $this->itemTotals(clone $items, [AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::IncludedInPayout]),
                'paid' => $this->itemTotals(clone $items, [AffiliateCommissionItemStatus::Paid]),
                'conversion' => $totalClicks > 0 && $bookingCount <= $totalClicks ? round(($bookingCount / $totalClicks) * 100, 1) : null,
                'last_activity' => max(array_filter([(clone $clicks)->max('clicked_at'), (clone $bookings)->max('last_synced_at')])) ?: null,
            ];
        });

        $commissionItems = AffiliateCommissionItem::query()
            ->whereHas('booking', fn (Builder $query): Builder => $query->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()]))
            ->when($affiliateId, fn (Builder $query): Builder => $query->where('affiliate_id', $affiliateId))
            ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency))
            ->when($commissionStatus, fn (Builder $query): Builder => $query->where('status', $commissionStatus))
            ->when($reviewerId, fn (Builder $query): Builder => $query->where('reviewed_by', $reviewerId));
        $payouts = AffiliatePayout::query()
            ->whereBetween('created_at', [$range->from, $range->to])
            ->when($affiliateId, fn (Builder $query): Builder => $query->where('affiliate_id', $affiliateId))
            ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency))
            ->when($payoutStatus, fn (Builder $query): Builder => $query->where('status', $payoutStatus));

        return [
            'registration' => [
                'new' => (clone $registered)->count(),
                'self_registered' => (clone $registered)->where('registration_source', 'self_registration')->count(),
                'created_by_nandini' => (clone $registered)->where('registration_source', 'created_by_nandini')->count(),
            ],
            'statuses' => collect(AffiliateStatus::cases())->mapWithKeys(fn ($state): array => [$state->label() => (clone $registered)->where('status', $state)->count()])->all(),
            'commission_statuses' => collect(AffiliateCommissionItemStatus::cases())->mapWithKeys(fn ($state): array => [$state->label() => (clone $commissionItems)->where('status', $state)->count()])->all(),
            'payout_statuses' => collect(AffiliatePayoutStatus::cases())->mapWithKeys(fn ($state): array => [$state->label() => (clone $payouts)->where('status', $state)->count()])
                ->put('Overdue', (clone $payouts)->whereNotIn('status', [AffiliatePayoutStatus::Paid, AffiliatePayoutStatus::Cancelled])->where('due_at', '<', now())->count())->all(),
            'commission_totals' => (clone $commissionItems)->select('status', 'currency', DB::raw('SUM(COALESCE(approved_commission_amount, original_commission_amount)) amount'))->groupBy('status', 'currency')->orderBy('currency')->get(),
            'payout_totals' => (clone $payouts)->select('status', 'currency', DB::raw('COUNT(*) payout_count'), DB::raw('SUM(gross_commission_amount) gross_amount'), DB::raw('SUM(adjustment_amount) adjustment_amount'), DB::raw('SUM(net_payout_amount) net_amount'))->groupBy('status', 'currency')->orderBy('currency')->get(),
            'payment_profiles' => [
                'Complete' => (clone $affiliates)->whereHas('paymentProfile', fn (Builder $query): Builder => $query->where('is_complete', true))->count(),
                'Incomplete' => (clone $affiliates)->whereHas('paymentProfile', fn (Builder $query): Builder => $query->where('is_complete', false))->count(),
                'Missing' => (clone $affiliates)->whereDoesntHave('paymentProfile')->count(),
            ],
            'performance' => $performance,
            'exceptions' => $this->exceptions(),
        ];
    }

    public function affiliateRows(AffiliateReportDateRange $range, ?string $status = null, ?string $registrationSource = null, ?int $approverId = null): iterable
    {
        return Affiliate::query()->whereBetween('created_at', [$range->from, $range->to])
            ->when($status, fn (Builder $query): Builder => $query->where('status', $status))
            ->when($registrationSource, fn (Builder $query): Builder => $query->where('registration_source', $registrationSource))
            ->when($approverId, fn (Builder $query): Builder => $query->where('approved_by', $approverId))
            ->with(['creator', 'approver'])->orderBy('created_at')->get()->map(fn (Affiliate $affiliate): array => [
                $affiliate->name, $affiliate->email, $affiliate->phone_whatsapp, $affiliate->affiliate_code,
                $affiliate->status->label(), $affiliate->registration_source->label(), $affiliate->created_at->toDateString(),
                $affiliate->creator?->name ?? 'Self registration', $affiliate->approver?->name ?? '',
            ]);
    }

    public function performanceRows(AffiliateReportDateRange $range, ?string $status = null, ?string $currency = null, ?int $affiliateId = null, ?string $registrationSource = null): iterable
    {
        return collect($this->dashboard($range, $status, $currency, $affiliateId, $registrationSource)['performance'])->flatMap(function (array $row): array {
            $currencies = collect([...$row['estimated'], ...$row['approved'], ...$row['paid']])->pluck('currency')->unique()->sort();
            if ($currencies->isEmpty()) {
                $currencies = collect(['']);
            }

            return $currencies->map(fn (string $currency): array => [
                $row['name'], $row['code'], $row['status'], $row['clicks'], $row['unique'], $row['bookings'], $row['room_nights'],
                collect($row['estimated'])->firstWhere('currency', $currency)['amount'] ?? '',
                collect($row['approved'])->firstWhere('currency', $currency)['amount'] ?? '',
                collect($row['paid'])->firstWhere('currency', $currency)['amount'] ?? '',
                $currency, $row['conversion'] ?? '', $row['last_activity'] ?? '',
            ])->all();
        });
    }

    public function bookingRows(AffiliateReportDateRange $range, ?string $currency = null, ?int $affiliateId = null): iterable
    {
        return AffiliateBooking::query()->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()])
            ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency))
            ->when($affiliateId, fn (Builder $query): Builder => $query->where('affiliate_id', $affiliateId))
            ->with(['affiliate', 'rooms', 'commissionItem'])->orderBy('check_out_date')->get()->map(fn (AffiliateBooking $booking): array => [
                $booking->affiliate->name, $booking->affiliate_code_snapshot, $booking->roomTypesLabel(), $booking->check_in_date->toDateString(), $booking->check_out_date->toDateString(),
                $booking->stay_nights, $booking->booking_status->label(), $booking->commissionItem?->status?->label() ?? $booking->commission_state->label(),
                $booking->commissionItem?->approved_commission_amount ?? $booking->commissionItem?->original_commission_amount ?? $booking->estimated_commission_amount ?? '', $booking->currency ?? '',
            ]);
    }

    public function commissionRows(AffiliateReportDateRange $range, ?string $currency = null, ?int $affiliateId = null, ?string $status = null, ?int $reviewerId = null): iterable
    {
        return AffiliateCommissionItem::query()->whereHas('booking', fn (Builder $query): Builder => $query->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()]))
            ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency))
            ->when($affiliateId, fn (Builder $query): Builder => $query->where('affiliate_id', $affiliateId))
            ->when($status, fn (Builder $query): Builder => $query->where('status', $status))
            ->when($reviewerId, fn (Builder $query): Builder => $query->where('reviewed_by', $reviewerId))
            ->with(['affiliate', 'period', 'booking'])->orderBy('id')->get()->map(fn (AffiliateCommissionItem $item): array => [
                $item->period->label(), $item->affiliate->name, $item->affiliate->affiliate_code, $item->booking->check_out_date->toDateString(), $item->status->label(),
                $item->original_commission_amount, $item->approved_commission_amount ?? '', $item->currency, $item->source_changed_after_review ? 'Yes' : 'No',
            ]);
    }

    public function payoutRows(AffiliateReportDateRange $range, ?string $currency = null, ?int $affiliateId = null, ?string $status = null): iterable
    {
        return AffiliatePayout::query()->whereBetween('created_at', [$range->from, $range->to])
            ->when($currency, fn (Builder $query): Builder => $query->where('currency', $currency))
            ->when($affiliateId, fn (Builder $query): Builder => $query->where('affiliate_id', $affiliateId))
            ->when($status, fn (Builder $query): Builder => $query->where('status', $status))
            ->with('affiliate')->orderBy('created_at')->get()->map(fn (AffiliatePayout $payout): array => [
                $payout->payout_number, $payout->affiliate->name, $payout->gross_commission_amount, $payout->adjustment_amount, $payout->net_payout_amount,
                $payout->currency, str($payout->payment_method_snapshot)->replace('_', ' ')->title(), $payout->status->label(), $payout->due_at?->toDateString() ?? '', $payout->paid_at?->toDateString() ?? '',
            ]);
    }

    public function exceptions(): array
    {
        $lastSync = BookingSyncLog::query()->latest('started_at')->first();
        $unknownCodes = Schema::hasColumn('synced_webhotelier_bookings', 'affiliate_code') ? SyncedWebhotelierBooking::query()->whereNotNull('affiliate_code')->where('affiliate_code', '!=', '')->whereNotIn('affiliate_code', Affiliate::query()->whereNotNull('affiliate_code')->select('affiliate_code'))->count() : 0;
        $failedAffiliateJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->where('payload', 'like', '%Affiliate%')->count() : 0;

        return [
            'Pending reviews older than 48 hours' => Affiliate::query()->where('status', AffiliateStatus::Pending)->where('created_at', '<', now()->subHours(48))->count(),
            'Approved affiliates without payment details' => Affiliate::query()->where('status', AffiliateStatus::Approved)->whereDoesntHave('paymentProfile')->count(),
            'Unknown affiliate voucher codes' => $unknownCodes,
            'Bookings missing room revenue' => AffiliateBooking::query()->whereNull('room_revenue_amount')->count(),
            'Bookings with unknown statuses' => AffiliateBooking::query()->where('booking_status', 'unknown')->count(),
            'Commission items on hold' => AffiliateCommissionItem::query()->where('status', AffiliateCommissionItemStatus::Held)->count(),
            'Source-changed commission items' => AffiliateCommissionItem::query()->where('source_changed_after_review', true)->count(),
            'Payouts past due' => AffiliatePayout::query()->whereNotIn('status', [AffiliatePayoutStatus::Paid, AffiliatePayoutStatus::Cancelled])->where('due_at', '<', now())->count(),
            'Failed payouts' => AffiliatePayout::query()->where('status', AffiliatePayoutStatus::Failed)->count(),
            'Country detection unavailable' => AffiliateClickEvent::query()->where('is_bot', false)->whereNull('country_code')->count(),
            'Recent booking-sync failure' => $lastSync?->status === BookingSyncLog::STATUS_FAILED ? 1 : 0,
            'Failed Affiliate notification jobs' => $failedAffiliateJobs,
        ];
    }

    private function moneyTotals(Builder $query, string $column): array
    {
        return $query->whereNotNull($column)->whereNotNull('currency')->select('currency', DB::raw("SUM({$column}) amount"))->groupBy('currency')->orderBy('currency')->get()->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }

    private function itemTotals(Builder $query, array $statuses): array
    {
        return $query->whereIn('status', $statuses)->select('currency', DB::raw('SUM(COALESCE(approved_commission_amount, original_commission_amount)) amount'))->groupBy('currency')->orderBy('currency')->get()->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }
}
