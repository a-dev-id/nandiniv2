<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Resources\Pages\CreateRecord;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = trim((string) ($data['name'] ?? ''));

        if ($data['name'] === '') {
            $data['name'] = trim((string) ($data['first_name'] ?? '') . ' ' . (string) ($data['last_name'] ?? ''));
        }

        if ($data['name'] === '') {
            $data['name'] = (string) ($data['email'] ?? '');
        }

        $data['email'] = strtolower(trim((string) ($data['email'] ?? '')));
        $data['member_source'] = $data['member_source'] ?? Member::SOURCE_MANUAL_REGISTER;
        $data['tier'] = $data['tier'] ?? Member::TIER_BRONZE;
        $data['points'] = 0;
        $data['must_change_password'] = true;
        $data['membership_started_at'] = $data['membership_started_at'] ?? now();
        $data['membership_expires_at'] = $data['membership_expires_at'] ?? now()->addYear();
        $data['email_verified_at'] = $data['email_verified_at'] ?? now();

        return $data;
    }
}
