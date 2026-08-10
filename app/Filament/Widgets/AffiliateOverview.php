<?php

namespace App\Filament\Widgets;

use App\Enums\AffiliateCommissionItemStatus;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateCommissionItem;
use App\Models\Permission;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use App\Services\Affiliate\Reports\AffiliateOperationalReportService;
use Filament\Widgets\Widget;

class AffiliateOverview extends Widget
{
    protected string $view = 'filament.widgets.affiliate-overview';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user?->hasPermissionTo(Permission::AFFILIATE_VIEW) === true
            || $user?->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW) === true
            || $user?->hasPermissionTo(Permission::AFFILIATE_CLICK_VIEW) === true;
    }

    protected function getViewData(): array
    {
        $analytics = app(AffiliateClickAnalyticsService::class)->overview('30');
        $from = now()->timezone(config('app.timezone'))->subDays(29)->startOfDay();
        $exceptions = app(AffiliateOperationalReportService::class)->exceptions();
        $importantExceptions = collect([
            'Approved affiliates without payment details',
            'Unknown affiliate voucher codes',
            'Bookings missing room revenue',
            'Source-changed commission items',
            'Recent booking-sync failure',
            'Failed Affiliate notification jobs',
        ])->mapWithKeys(fn (string $label): array => [$label => (int) ($exceptions[$label] ?? 0)])->all();

        return [
            'analytics' => $analytics,
            'metrics' => [
                'new_affiliates' => Affiliate::query()->where('created_at', '>=', $from)->count(),
                'total_clicks' => $analytics['summary']['total'],
                'unique_clicks' => $analytics['summary']['unique'],
                'tracked_bookings' => AffiliateBooking::query()->where('created_at', '>=', $from)->count(),
                'pending_commissions' => AffiliateCommissionItem::query()->whereIn('status', [
                    AffiliateCommissionItemStatus::PendingReview,
                    AffiliateCommissionItemStatus::Held,
                    AffiliateCommissionItemStatus::Approved,
                    AffiliateCommissionItemStatus::IncludedInPayout,
                ])->count(),
                'paid_commissions' => AffiliateCommissionItem::query()->where('status', AffiliateCommissionItemStatus::Paid)->where('updated_at', '>=', $from)->count(),
            ],
            'importantExceptions' => $importantExceptions,
        ];
    }
}
