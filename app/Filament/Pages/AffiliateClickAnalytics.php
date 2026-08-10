<?php

namespace App\Filament\Pages;

use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Permission;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;
use UnitEnum;

class AffiliateClickAnalytics extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Click Analytics';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 30;

    protected static ?string $title = 'Affiliate Click Analytics';

    protected string $view = 'filament.pages.affiliate-click-analytics';

    #[Url]
    public string $range = '30';

    #[Url]
    public string $affiliateId = '';

    #[Url]
    public string $status = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo(Permission::AFFILIATE_CLICK_VIEW) === true;
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $range = in_array($this->range, AffiliateClickAnalyticsService::RANGES, true) ? $this->range : '30';
        $affiliateId = ctype_digit($this->affiliateId) && Affiliate::query()->whereKey((int) $this->affiliateId)->exists()
            ? (int) $this->affiliateId
            : null;
        $statuses = array_column(AffiliateStatus::cases(), 'value');
        $status = in_array($this->status, $statuses, true) ? $this->status : null;

        return [
            'analytics' => app(AffiliateClickAnalyticsService::class)->overview($range, $affiliateId, $status),
            'affiliates' => Affiliate::query()->orderBy('name')->get(['id', 'name', 'affiliate_code']),
            'statuses' => AffiliateStatus::cases(),
        ];
    }
}
