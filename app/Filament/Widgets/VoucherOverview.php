<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\IssuedVouchers\IssuedVoucherResource;
use App\Filament\Resources\VoucherOrders\VoucherOrderResource;
use App\Filament\Resources\VoucherRedemptions\VoucherRedemptionResource;
use App\Models\IssuedVoucher;
use App\Models\VoucherOrderItem;
use App\Models\VoucherRedemption;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoucherOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 30;

    protected ?string $heading = 'Voucher Overview';

    protected ?string $description = 'Paid purchases, active vouchers, and guest redemption activity.';

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $purchasedCount = (int) VoucherOrderItem::query()
            ->whereHas('order', fn($query) => $query->paid())
            ->sum('quantity');

        $mostPurchased = VoucherOrderItem::query()
            ->select('voucher_title')
            ->selectRaw('SUM(quantity) as aggregate')
            ->whereHas('order', fn($query) => $query->paid())
            ->groupBy('voucher_title')
            ->orderByDesc('aggregate')
            ->orderBy('voucher_title')
            ->first();

        $mostUsedAtResort = VoucherRedemption::query()
            ->join('issued_vouchers', 'issued_vouchers.id', '=', 'voucher_redemptions.issued_voucher_id')
            ->select('issued_vouchers.title')
            ->selectRaw('COUNT(voucher_redemptions.id) as aggregate')
            ->groupBy('issued_vouchers.title')
            ->orderByDesc('aggregate')
            ->orderBy('issued_vouchers.title')
            ->first();

        return [
            Stat::make('Vouchers Purchased', number_format($purchasedCount))
                ->description('Total voucher quantity from paid orders')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary')
                ->url(VoucherOrderResource::getUrl()),

            Stat::make('Active Vouchers', number_format(IssuedVoucher::query()->whereIn('status', ['active', 'partially_redeemed'])->count()))
                ->description('Available for full or partial redemption')
                ->icon('heroicon-o-ticket')
                ->color('success')
                ->url(IssuedVoucherResource::getUrl()),

            Stat::make('Most Purchased Voucher', $mostPurchased?->voucher_title ?? 'No paid purchases yet')
                ->description($mostPurchased ? number_format((int) $mostPurchased->aggregate) . ' purchased' : 'Waiting for the first paid order')
                ->icon('heroicon-o-trophy')
                ->color('warning')
                ->url(VoucherOrderResource::getUrl()),

            Stat::make('Most Used at Resort', $mostUsedAtResort?->title ?? 'No redemptions yet')
                ->description($mostUsedAtResort ? number_format((int) $mostUsedAtResort->aggregate) . ' redemption visit(s)' : 'Waiting for the first redemption')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->url(VoucherRedemptionResource::getUrl()),
        ];
    }
}
