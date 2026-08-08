<?php

namespace App\Filament\Pages;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Affiliate\Reports\AffiliateOperationalReportService;
use App\Services\Affiliate\Reports\AffiliateReportDateRange;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;
use UnitEnum;

class AffiliateOperationalReports extends Page
{
    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Reports';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Management';

    protected static ?int $navigationSort = 19;

    protected static ?string $title = 'Affiliate Operational Reports';

    protected string $view = 'filament.pages.affiliate-operational-reports';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $currency = '';

    #[Url]
    public string $affiliateId = '';

    #[Url]
    public string $registrationSource = '';

    #[Url]
    public string $approverId = '';

    #[Url]
    public string $commissionStatus = '';

    #[Url]
    public string $payoutStatus = '';

    #[Url]
    public string $reviewerId = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW) === true;
    }

    protected function getViewData(): array
    {
        $now = CarbonImmutable::now(config('app.timezone'));
        $from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->from) ? CarbonImmutable::parse($this->from)->startOfDay() : $now->startOfMonth();
        $to = preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->to) ? CarbonImmutable::parse($this->to)->endOfDay() : $now->endOfDay();
        if ($from->gt($to) || $from->diffInDays($to) > 731) {
            $from = $now->startOfMonth();
            $to = $now->endOfDay();
        }
        $range = new AffiliateReportDateRange($from, $to, 'custom');
        $statuses = array_column(AffiliateStatus::cases(), 'value');
        $sources = array_column(AffiliateRegistrationSource::cases(), 'value');
        $commissionStatuses = array_column(AffiliateCommissionItemStatus::cases(), 'value');
        $payoutStatuses = array_column(AffiliatePayoutStatus::cases(), 'value');
        $affiliateId = ctype_digit($this->affiliateId) ? (int) $this->affiliateId : null;
        $approverId = ctype_digit($this->approverId) ? (int) $this->approverId : null;
        $reviewerId = ctype_digit($this->reviewerId) ? (int) $this->reviewerId : null;

        return [
            'report' => app(AffiliateOperationalReportService::class)->dashboard(
                $range,
                in_array($this->status, $statuses, true) ? $this->status : null,
                preg_match('/^[A-Z]{3,10}$/', $this->currency) ? $this->currency : null,
                $affiliateId,
                in_array($this->registrationSource, $sources, true) ? $this->registrationSource : null,
                $approverId,
                in_array($this->commissionStatus, $commissionStatuses, true) ? $this->commissionStatus : null,
                in_array($this->payoutStatus, $payoutStatuses, true) ? $this->payoutStatus : null,
                $reviewerId,
            ),
            'range' => $range,
            'statuses' => AffiliateStatus::cases(),
            'registrationSources' => AffiliateRegistrationSource::cases(),
            'commissionStatuses' => AffiliateCommissionItemStatus::cases(),
            'payoutStatuses' => AffiliatePayoutStatus::cases(),
            'affiliates' => Affiliate::query()->orderBy('name')->get(['id', 'name', 'affiliate_code']),
            'approvers' => User::query()->whereHas('roles', fn ($query) => $query->whereIn('slug', [Role::ADMINISTRATOR, Role::SALES_MARKETING]))->orderBy('name')->get(['id', 'name']),
            'reviewers' => User::query()->whereHas('roles', fn ($query) => $query->whereIn('slug', [Role::ADMINISTRATOR, Role::FINANCE]))->orderBy('name')->get(['id', 'name']),
            'currencies' => AffiliateBooking::query()->whereNotNull('currency')->distinct()->orderBy('currency')->pluck('currency'),
        ];
    }
}
