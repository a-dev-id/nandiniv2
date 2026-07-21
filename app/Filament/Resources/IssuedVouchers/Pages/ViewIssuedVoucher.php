<?php

namespace App\Filament\Resources\IssuedVouchers\Pages;

use App\Filament\Resources\IssuedVouchers\IssuedVoucherResource;
use Filament\Resources\Pages\ViewRecord;

class ViewIssuedVoucher extends ViewRecord
{
    protected static string $resource = IssuedVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            IssuedVoucherResource::redeemAction(),
        ];
    }
}
