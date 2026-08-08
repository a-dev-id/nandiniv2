<?php

namespace App\Services\Affiliate\Reports;

use App\Enums\AffiliateCommissionItemStatus;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateBookingRoom;
use App\Models\AffiliateClickEvent;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliatePayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AffiliateReportService
{
    public function summary(Affiliate $affiliate, AffiliateReportDateRange $range): array
    {
        $clicks = AffiliateClickEvent::query()->where('affiliate_id', $affiliate->id)->where('is_bot', false)
            ->whereBetween('clicked_at', [$range->from, $range->to]);
        $bookings = AffiliateBooking::query()->where('affiliate_id', $affiliate->id)
            ->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()]);
        $items = AffiliateCommissionItem::query()->where('affiliate_id', $affiliate->id)
            ->whereHas('booking', fn (Builder $query): Builder => $query->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()]));

        return [
            'total_clicks' => (clone $clicks)->count(),
            'unique_clicks' => (clone $clicks)->where('is_unique', true)->count(),
            'tracked_bookings' => (clone $bookings)->count(),
            'room_nights' => (int) AffiliateBookingRoom::query()->whereHas('booking', fn (Builder $query): Builder => $query->where('affiliate_id', $affiliate->id)->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()]))->sum(DB::raw('stay_nights * room_quantity')),
            'estimated' => $this->bookingTotals(clone $bookings, 'estimated_commission_amount'),
            'pending_validation' => $this->itemTotals(clone $items, [AffiliateCommissionItemStatus::PendingReview, AffiliateCommissionItemStatus::Held]),
            'approved_unpaid' => $this->itemTotals(clone $items, [AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::IncludedInPayout]),
            'paid' => $this->itemTotals(clone $items, [AffiliateCommissionItemStatus::Paid]),
            'conversion' => $this->conversion((clone $clicks)->count(), (clone $bookings)->count()),
        ];
    }

    public function clickRows(Affiliate $affiliate, AffiliateReportDateRange $range): iterable
    {
        return AffiliateClickEvent::query()->where('affiliate_id', $affiliate->id)->where('is_bot', false)
            ->whereBetween('clicked_at', [$range->from, $range->to])
            ->select('click_date', DB::raw('COUNT(*) total'), DB::raw('SUM(CASE WHEN is_unique = 1 THEN 1 ELSE 0 END) unique_clicks'))
            ->groupBy('click_date')->orderBy('click_date')->get()->map(function ($row) use ($affiliate): array {
                $base = AffiliateClickEvent::query()->where('affiliate_id', $affiliate->id)->where('is_bot', false)->whereDate('click_date', $row->click_date);
                $country = (clone $base)->whereNotNull('country_name')->select('country_name', DB::raw('COUNT(*) aggregate'))->groupBy('country_name')->orderByDesc('aggregate')->value('country_name');
                $device = (clone $base)->select('device_type', DB::raw('COUNT(*) aggregate'))->groupBy('device_type')->orderByDesc('aggregate')->value('device_type');

                return [$row->click_date->toDateString(), $row->total, $row->unique_clicks, $country ?: 'Unknown', $device ? str($device)->title() : 'Unknown'];
            });
    }

    public function bookingRows(Affiliate $affiliate, AffiliateReportDateRange $range): iterable
    {
        return AffiliateBooking::query()->where('affiliate_id', $affiliate->id)
            ->whereBetween('check_out_date', [$range->from->toDateString(), $range->to->toDateString()])
            ->with(['rooms', 'commissionItem'])->orderBy('check_out_date')->get()->map(function (AffiliateBooking $booking): array {
                $item = $booking->commissionItem;

                return [
                    $booking->roomTypesLabel(), $booking->check_in_date->toDateString(), $booking->check_out_date->toDateString(),
                    $booking->stay_nights, $booking->booking_status->label(), $item?->status?->label() ?? $booking->commission_state->label(),
                    $item?->approved_commission_amount ?? $item?->original_commission_amount ?? $booking->estimated_commission_amount ?? '', $booking->currency ?? '',
                ];
            });
    }

    public function payoutRows(Affiliate $affiliate, AffiliateReportDateRange $range): iterable
    {
        return AffiliatePayout::query()->where('affiliate_id', $affiliate->id)
            ->whereBetween('created_at', [$range->from, $range->to])->orderBy('created_at')->get()->map(fn (AffiliatePayout $payout): array => [
                $payout->payout_number, $payout->net_payout_amount, $payout->currency,
                str($payout->payment_method_snapshot)->replace('_', ' ')->title(), $payout->status->label(), $payout->paid_at?->toDateString() ?? '',
            ]);
    }

    private function bookingTotals(Builder $query, string $column): array
    {
        return $query->whereNotNull($column)->whereNotNull('currency')->select('currency', DB::raw("SUM({$column}) amount"))->groupBy('currency')->orderBy('currency')->get()
            ->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }

    private function itemTotals(Builder $query, array $statuses): array
    {
        return $query->whereIn('status', $statuses)->select('currency', DB::raw('SUM(COALESCE(approved_commission_amount, original_commission_amount)) amount'))->groupBy('currency')->orderBy('currency')->get()
            ->map(fn ($row): array => ['currency' => $row->currency, 'amount' => (string) $row->getRawOriginal('amount')])->all();
    }

    private function conversion(int $clicks, int $bookings): array
    {
        if ($clicks === 0 || $bookings > $clicks) {
            return ['percentage' => null, 'note' => $bookings > $clicks ? 'Not shown because manual affiliate-code bookings exceed tracked clicks in this period.' : 'No tracked clicks in this period.'];
        }

        return ['percentage' => round(($bookings / $clicks) * 100, 1), 'note' => 'Tracked bookings divided by non-bot clicks. Manual codes and delayed synchronization can affect this indicator.'];
    }
}
