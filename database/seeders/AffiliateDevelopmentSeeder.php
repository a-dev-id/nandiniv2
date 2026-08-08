<?php

namespace Database\Seeders;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AffiliateDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $internalUsers = [
            ['name' => 'Local Affiliate Administrator', 'email' => 'affiliate.admin.local@nandinibali.test', 'role' => Role::ADMINISTRATOR],
            ['name' => 'Local Affiliate Sales', 'email' => 'affiliate.sales.local@nandinibali.test', 'role' => Role::SALES_MARKETING],
            ['name' => 'Local Affiliate Finance', 'email' => 'affiliate.finance.local@nandinibali.test', 'role' => Role::FINANCE],
        ];

        foreach ($internalUsers as $internalUser) {
            $user = User::query()->updateOrCreate([
                'email' => $internalUser['email'],
            ], [
                'name' => $internalUser['name'],
                'password' => Hash::make('LocalAdmin!2026'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($internalUser['role']);
        }

        $records = [
            [
                'name' => 'Local Pending Affiliate',
                'email' => 'partner.pending.local@nandinibali.test',
                'status' => AffiliateStatus::Pending,
            ],
            [
                'name' => 'Local Approved Affiliate',
                'email' => 'partner.approved.local@nandinibali.test',
                'status' => AffiliateStatus::Approved,
            ],
        ];

        foreach ($records as $record) {
            $registeredAt = now();
            $baseCode = str($record['name'])->ascii()->lower()->replaceMatches('/[^a-z0-9]/', '')->toString()
                .$registeredAt->day.$registeredAt->month.$registeredAt->format('y');
            $code = $baseCode;
            $sequence = 2;

            while (Affiliate::query()->where('affiliate_code', $code)->where('email', '!=', $record['email'])->exists()) {
                $code = $baseCode.str_pad((string) $sequence++, 2, '0', STR_PAD_LEFT);
            }

            $affiliate = Affiliate::query()->updateOrCreate([
                'email' => $record['email'],
            ], [
                'name' => $record['name'],
                'password' => Hash::make('LocalAffiliate!2026'),
                'email_verified_at' => now(),
                'phone_whatsapp' => '+62 000 0000 0000',
                'status' => $record['status'],
                'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
                'affiliate_code' => $code,
                'affiliate_code_generated_at' => $registeredAt,
                'short_link_slug' => $code,
                'short_link_activated_at' => $record['status'] === AffiliateStatus::Approved ? $registeredAt : null,
            ]);
            $affiliate->assignRole(Role::AFFILIATE);
        }
    }
}
