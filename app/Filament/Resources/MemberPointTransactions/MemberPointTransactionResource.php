<?php

namespace App\Filament\Resources\MemberPointTransactions;

use App\Filament\Resources\MemberPointTransactions\Pages\EditMemberPointTransaction;
use App\Filament\Resources\MemberPointTransactions\Pages\ListMemberPointTransactions;
use App\Filament\Resources\MemberPointTransactions\Schemas\MemberPointTransactionForm;
use App\Filament\Resources\MemberPointTransactions\Tables\MemberPointTransactionsTable;
use App\Models\MemberPointTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MemberPointTransactionResource extends Resource
{
    protected static ?string $model = MemberPointTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $navigationLabel = 'Point History';

    protected static ?string $modelLabel = 'Point Transaction';

    protected static ?string $pluralModelLabel = 'Point History';

    protected static string | UnitEnum | null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return MemberPointTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberPointTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberPointTransactions::route('/'),
            'edit' => EditMemberPointTransaction::route('/{record}/edit'),
        ];
    }
}
