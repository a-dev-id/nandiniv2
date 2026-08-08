<?php

namespace App\Filament\Resources\AffiliatePayoutMinimums\Pages;

use App\Filament\Resources\AffiliatePayoutMinimums\AffiliatePayoutMinimumResource;
use Filament\Resources\Pages\EditRecord;

class EditAffiliatePayoutMinimum extends EditRecord
{
    protected static string $resource = AffiliatePayoutMinimumResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['currency'] = mb_strtoupper($data['currency']);

        return $data;
    }
}
