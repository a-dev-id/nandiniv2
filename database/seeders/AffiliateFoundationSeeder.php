<?php

namespace Database\Seeders;

use App\Models\AffiliatePayoutMinimum;
use App\Models\AffiliateProgramSetting;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AffiliateFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect(Permission::affiliatePermissionNames())
            ->mapWithKeys(fn (string $name): array => [
                $name => Permission::query()->firstOrCreate(['name' => $name]),
            ]);

        $roles = collect([
            Role::ADMINISTRATOR => 'Administrator',
            Role::SALES_MARKETING => 'Sales & Marketing',
            Role::FINANCE => 'Finance',
            Role::AFFILIATE => 'Affiliate',
        ])->mapWithKeys(fn (string $name, string $slug): array => [
            $slug => Role::query()->updateOrCreate(['slug' => $slug], ['name' => $name]),
        ]);

        $rolePermissions = [
            Role::ADMINISTRATOR => Permission::affiliatePermissionNames(),
            Role::SALES_MARKETING => [
                Permission::AFFILIATE_VIEW,
                Permission::AFFILIATE_CREATE,
                Permission::AFFILIATE_UPDATE,
                Permission::AFFILIATE_APPROVE,
                Permission::AFFILIATE_REJECT,
                Permission::AFFILIATE_SUSPEND,
                Permission::AFFILIATE_REACTIVATE,
                Permission::AFFILIATE_BOOKING_VIEW,
                Permission::AFFILIATE_BOOKING_MANAGE,
                Permission::AFFILIATE_CLICK_VIEW,
                Permission::AFFILIATE_REPORT_VIEW,
                Permission::AFFILIATE_MARKETING_ASSET_MANAGE,
            ],
            Role::FINANCE => [
                Permission::AFFILIATE_BOOKING_VIEW,
                Permission::AFFILIATE_COMMISSION_VIEW,
                Permission::AFFILIATE_COMMISSION_VALIDATE,
                Permission::AFFILIATE_COMMISSION_APPROVE,
                Permission::AFFILIATE_PAYOUT_VIEW,
                Permission::AFFILIATE_PAYOUT_MANAGE,
                Permission::AFFILIATE_SETTING_MANAGE,
                Permission::AFFILIATE_PAYMENT_PROFILE_VIEW,
                Permission::AFFILIATE_PAYMENT_PROFILE_MANAGE,
                Permission::AFFILIATE_REPORT_VIEW,
            ],
            Role::AFFILIATE => [
                Permission::AFFILIATE_DASHBOARD_VIEW_OWN,
                Permission::AFFILIATE_PROFILE_VIEW_OWN,
                Permission::AFFILIATE_PROFILE_UPDATE_OWN,
                Permission::AFFILIATE_BOOKING_VIEW_OWN,
                Permission::AFFILIATE_COMMISSION_VIEW_OWN,
                Permission::AFFILIATE_CLICK_VIEW_OWN,
                Permission::AFFILIATE_PAYOUT_VIEW_OWN,
                Permission::AFFILIATE_PAYMENT_PROFILE_UPDATE_OWN,
                Permission::AFFILIATE_MARKETING_ASSET_VIEW_OWN,
                Permission::AFFILIATE_REPORT_VIEW_OWN,
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionNames) {
            $roles[$roleSlug]->permissions()->sync(
                $permissions->only($permissionNames)->pluck('id')->all()
            );
        }

        Permission::query()
            ->where('name', 'like', 'affiliate%')
            ->whereNotIn('name', Permission::affiliatePermissionNames())
            ->delete();

        // Wise and the IDR 500,000 minimum payout remain subject to Finance confirmation.
        AffiliateProgramSetting::current();

        AffiliatePayoutMinimum::query()->firstOrCreate(['currency' => 'IDR'], [
            'minimum_amount' => '500000.00',
            'is_active' => true,
        ]);
    }
}
