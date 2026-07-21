<?php

namespace App\Filament\Resources\MemberRewardRedemptions;

use App\Filament\Resources\MemberRewardRedemptions\Pages\EditMemberRewardRedemption;
use App\Filament\Resources\MemberRewardRedemptions\Pages\ListMemberRewardRedemptions;
use App\Filament\Resources\MemberRewardRedemptions\Schemas\MemberRewardRedemptionForm;
use App\Filament\Resources\MemberRewardRedemptions\Tables\MemberRewardRedemptionsTable;
use App\Models\MemberRewardRedemption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MemberRewardRedemptionResource extends Resource
{
    protected static ?string $model = MemberRewardRedemption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Reward Redemptions';

    protected static ?string $modelLabel = 'Reward Redemption';

    protected static ?string $pluralModelLabel = 'Reward Redemptions';

    protected static string | UnitEnum | null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return MemberRewardRedemptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MemberRewardRedemptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberRewardRedemptions::route('/'),
            'edit' => EditMemberRewardRedemption::route('/{record}/edit'),
        ];
    }
}
