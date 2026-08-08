<?php

namespace App\Filament\Pages;

use App\Models\Permission;
use App\Services\Affiliate\Operations\AffiliateSystemHealthService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AffiliateSystemHealth extends Page
{
    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $navigationLabel = 'System Health';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate System';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Affiliate System Health';

    protected string $view = 'filament.pages.affiliate-system-health';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo(Permission::AFFILIATE_SYSTEM_HEALTH_VIEW) === true;
    }

    protected function getViewData(): array
    {
        return ['checks' => app(AffiliateSystemHealthService::class)->checks()];
    }
}
