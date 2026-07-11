<?php

namespace App\Filament\Resources\IssuedVouchers\Pages;

use App\Filament\Resources\IssuedVouchers\IssuedVoucherResource;
use Filament\Resources\Pages\ListRecords;

class ListIssuedVouchers extends ListRecords
{
    protected static string $resource = IssuedVoucherResource::class;
}
