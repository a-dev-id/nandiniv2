<?php

namespace App\Filament\Resources\Affiliates\Pages;

use App\Filament\Resources\Affiliates\AffiliateResource;
use App\Services\Affiliate\UpdatePendingAffiliateService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAffiliate extends EditRecord
{
    protected static string $resource = AffiliateResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UpdatePendingAffiliateService::class)->update($record, $data, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return AffiliateResource::getUrl('view', ['record' => $this->record]);
    }
}
