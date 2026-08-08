<?php

namespace App\Filament\Resources\AffiliatePayoutMinimums\Pages;

use App\Filament\Resources\AffiliatePayoutMinimums\AffiliatePayoutMinimumResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAffiliatePayoutMinimum extends CreateRecord
{
    protected static string $resource = AffiliatePayoutMinimumResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['currency'] = mb_strtoupper($data['currency']);

        return $data;
    }
}
