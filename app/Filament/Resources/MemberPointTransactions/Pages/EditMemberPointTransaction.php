<?php

namespace App\Filament\Resources\MemberPointTransactions\Pages;

use App\Filament\Resources\MemberPointTransactions\MemberPointTransactionResource;
use Filament\Resources\Pages\EditRecord;

class EditMemberPointTransaction extends EditRecord
{
    protected static string $resource = MemberPointTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
