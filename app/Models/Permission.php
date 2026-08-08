<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    public const AFFILIATE_DASHBOARD_VIEW_OWN = 'affiliate-dashboard.view-own';

    public const AFFILIATE_PROFILE_VIEW_OWN = 'affiliate-profile.view-own';

    public const AFFILIATE_PROFILE_UPDATE_OWN = 'affiliate-profile.update-own';

    public const AFFILIATE_BOOKING_VIEW_OWN = 'affiliate-booking.view-own';

    public const AFFILIATE_COMMISSION_VIEW_OWN = 'affiliate-commission.view-own';

    public const AFFILIATE_CLICK_VIEW_OWN = 'affiliate-click.view-own';

    public const AFFILIATE_PAYOUT_VIEW_OWN = 'affiliate-payout.view-own';

    public const AFFILIATE_VIEW = 'affiliate.view';

    public const AFFILIATE_CREATE = 'affiliate.create';

    public const AFFILIATE_UPDATE = 'affiliate.update';

    public const AFFILIATE_APPROVE = 'affiliate.approve';

    public const AFFILIATE_REJECT = 'affiliate.reject';

    public const AFFILIATE_SUSPEND = 'affiliate.suspend';

    public const AFFILIATE_REACTIVATE = 'affiliate.reactivate';

    public const AFFILIATE_BOOKING_VIEW = 'affiliate-booking.view';

    public const AFFILIATE_BOOKING_MANAGE = 'affiliate-booking.manage';

    public const AFFILIATE_COMMISSION_VIEW = 'affiliate-commission.view';

    public const AFFILIATE_COMMISSION_VALIDATE = 'affiliate-commission.validate';

    public const AFFILIATE_COMMISSION_APPROVE = 'affiliate-commission.approve';

    public const AFFILIATE_PAYOUT_VIEW = 'affiliate-payout.view';

    public const AFFILIATE_PAYOUT_MANAGE = 'affiliate-payout.manage';

    public const AFFILIATE_PAYMENT_PROFILE_VIEW = 'affiliate-payment-profile.view';

    public const AFFILIATE_PAYMENT_PROFILE_MANAGE = 'affiliate-payment-profile.manage';

    public const AFFILIATE_PAYMENT_PROFILE_UPDATE_OWN = 'affiliate-payment-profile.update-own';

    public const AFFILIATE_CLICK_VIEW = 'affiliate-click.view';

    public const AFFILIATE_REPORT_VIEW = 'affiliate-report.view';

    public const AFFILIATE_SETTING_MANAGE = 'affiliate-setting.manage';

    public const AFFILIATE_MARKETING_ASSET_MANAGE = 'affiliate-marketing-asset.manage';

    public const AFFILIATE_MARKETING_ASSET_VIEW_OWN = 'affiliate-marketing-asset.view-own';

    public const AFFILIATE_REPORT_VIEW_OWN = 'affiliate-report.view-own';

    public const AFFILIATE_SYSTEM_HEALTH_VIEW = 'affiliate-system-health.view';

    protected $fillable = ['name'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }

    /**
     * @return array<int, string>
     */
    public static function affiliatePermissionNames(): array
    {
        return [
            self::AFFILIATE_DASHBOARD_VIEW_OWN,
            self::AFFILIATE_PROFILE_VIEW_OWN,
            self::AFFILIATE_PROFILE_UPDATE_OWN,
            self::AFFILIATE_BOOKING_VIEW_OWN,
            self::AFFILIATE_COMMISSION_VIEW_OWN,
            self::AFFILIATE_CLICK_VIEW_OWN,
            self::AFFILIATE_PAYOUT_VIEW_OWN,
            self::AFFILIATE_VIEW,
            self::AFFILIATE_CREATE,
            self::AFFILIATE_UPDATE,
            self::AFFILIATE_APPROVE,
            self::AFFILIATE_REJECT,
            self::AFFILIATE_SUSPEND,
            self::AFFILIATE_REACTIVATE,
            self::AFFILIATE_BOOKING_VIEW,
            self::AFFILIATE_BOOKING_MANAGE,
            self::AFFILIATE_COMMISSION_VIEW,
            self::AFFILIATE_COMMISSION_VALIDATE,
            self::AFFILIATE_COMMISSION_APPROVE,
            self::AFFILIATE_PAYOUT_VIEW,
            self::AFFILIATE_PAYOUT_MANAGE,
            self::AFFILIATE_PAYMENT_PROFILE_VIEW,
            self::AFFILIATE_PAYMENT_PROFILE_MANAGE,
            self::AFFILIATE_PAYMENT_PROFILE_UPDATE_OWN,
            self::AFFILIATE_CLICK_VIEW,
            self::AFFILIATE_REPORT_VIEW,
            self::AFFILIATE_SETTING_MANAGE,
            self::AFFILIATE_MARKETING_ASSET_MANAGE,
            self::AFFILIATE_MARKETING_ASSET_VIEW_OWN,
            self::AFFILIATE_REPORT_VIEW_OWN,
            self::AFFILIATE_SYSTEM_HEALTH_VIEW,
        ];
    }
}
