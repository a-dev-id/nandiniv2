<?php

namespace App\Filament\Pages;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliatePayout;
use App\Models\Permission;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class AffiliateFinanceOverview extends Page
{
    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = 'Finance Overview';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Finance';

    protected static ?int $navigationSort = 19;

    protected static ?string $title = 'Affiliate Finance Overview';

    protected string $view = 'filament.pages.affiliate-finance-overview';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VIEW) === true;
    }

    protected function getViewData(): array
    {
        return [
            'counts' => [
                'pending_review' => AffiliateCommissionItem::query()->where('status', AffiliateCommissionItemStatus::PendingReview)->count(),
                'held' => AffiliateCommissionItem::query()->where('status', AffiliateCommissionItemStatus::Held)->count(),
                'ready' => AffiliatePayout::query()->where('status', AffiliatePayoutStatus::Ready)->count(),
                'processing' => AffiliatePayout::query()->where('status', AffiliatePayoutStatus::Processing)->count(),
                'overdue' => AffiliatePayout::query()->whereNotIn('status', [AffiliatePayoutStatus::Paid, AffiliatePayoutStatus::Cancelled])->where('due_at', '<', now())->count(),
            ],
            'approved' => $this->itemTotals([AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::IncludedInPayout]),
            'paidThisMonth' => AffiliatePayout::query()->where('status', AffiliatePayoutStatus::Paid)->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->select('currency', DB::raw('SUM(net_payout_amount) amount'))->groupBy('currency')->orderBy('currency')->get(),
        ];
    }

    private function itemTotals(array $statuses)
    {
        return AffiliateCommissionItem::query()->whereIn('status', $statuses)->select('currency', DB::raw('SUM(COALESCE(approved_commission_amount, original_commission_amount)) amount'))->groupBy('currency')->orderBy('currency')->get();
    }
}
