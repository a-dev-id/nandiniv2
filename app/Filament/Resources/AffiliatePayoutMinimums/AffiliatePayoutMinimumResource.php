<?php

namespace App\Filament\Resources\AffiliatePayoutMinimums;

use App\Filament\Resources\AffiliatePayoutMinimums\Pages\CreateAffiliatePayoutMinimum;
use App\Filament\Resources\AffiliatePayoutMinimums\Pages\EditAffiliatePayoutMinimum;
use App\Filament\Resources\AffiliatePayoutMinimums\Pages\ListAffiliatePayoutMinimums;
use App\Models\AffiliatePayoutMinimum;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AffiliatePayoutMinimumResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = AffiliatePayoutMinimum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Payout Minimums';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Finance';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('currency')->required()->length(3)->unique(ignoreRecord: true)->formatStateUsing(fn (?string $state): ?string => $state ? mb_strtoupper($state) : null),
            TextInput::make('minimum_amount')->required()->numeric()->minValue(0)->rules(['regex:/^\d{1,13}(?:\.\d{1,2})?$/']),
            Toggle::make('is_active')->required()->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('currency')->searchable()->sortable(),
            TextColumn::make('minimum_amount')->numeric(decimalPlaces: 2)->sortable(),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('updated_at')->dateTime('d M Y H:i')->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListAffiliatePayoutMinimums::route('/'), 'create' => CreateAffiliatePayoutMinimum::route('/create'), 'edit' => EditAffiliatePayoutMinimum::route('/{record}/edit')];
    }
}
