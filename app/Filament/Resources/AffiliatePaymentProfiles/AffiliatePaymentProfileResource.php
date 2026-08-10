<?php

namespace App\Filament\Resources\AffiliatePaymentProfiles;

use App\Enums\AffiliatePaymentMethod;
use App\Filament\Resources\AffiliatePaymentProfiles\Pages\ListAffiliatePaymentProfiles;
use App\Filament\Resources\AffiliatePaymentProfiles\Pages\ViewAffiliatePaymentProfile;
use App\Models\AffiliatePaymentProfile;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AffiliatePaymentProfileResource extends Resource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $model = AffiliatePaymentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Payment Profiles';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 60;

    public static function table(Table $table): Table
    {
        return $table->defaultSort('updated_at', 'desc')->columns([
            TextColumn::make('affiliate.name')->searchable()->sortable(),
            TextColumn::make('payment_method')->label('Method')->formatStateUsing(fn (AffiliatePaymentMethod $state): string => $state->label()),
            TextColumn::make('preferred_currency')->label('Currency'),
            TextColumn::make('masked_details')->label('Masked Details')->state(fn (AffiliatePaymentProfile $record): string => $record->maskedDetails()),
            IconColumn::make('is_complete')->label('Complete')->boolean(),
            TextColumn::make('verified_at')->label('Approved')->dateTime('d M Y H:i')->placeholder('Incomplete'),
            TextColumn::make('updated_at')->dateTime('d M Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('payment_method')->options(collect(AffiliatePaymentMethod::cases())->mapWithKeys(fn ($method): array => [$method->value => $method->label()])->all()),
            SelectFilter::make('is_complete')->options([1 => 'Complete', 0 => 'Incomplete']),
        ])->recordUrl(fn (AffiliatePaymentProfile $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Authorized Finance view')->description('These decrypted details must not be copied to general reports or audit notes.')->schema([
                TextEntry::make('affiliate.name'),
                TextEntry::make('payment_method')->formatStateUsing(fn (AffiliatePaymentMethod $state): string => $state->label()),
                TextEntry::make('preferred_currency')->label('Preferred Currency'),
                TextEntry::make('account_holder_name'),
                TextEntry::make('wise_email')->visible(fn (AffiliatePaymentProfile $record): bool => $record->payment_method === AffiliatePaymentMethod::Wise)->placeholder('—'),
                TextEntry::make('bank_name')->visible(fn (AffiliatePaymentProfile $record): bool => $record->payment_method === AffiliatePaymentMethod::BankTransfer)->placeholder('—'),
                TextEntry::make('bank_account_name')->visible(fn (AffiliatePaymentProfile $record): bool => $record->payment_method === AffiliatePaymentMethod::BankTransfer)->placeholder('—'),
                TextEntry::make('bank_account_number')->visible(fn (AffiliatePaymentProfile $record): bool => $record->payment_method === AffiliatePaymentMethod::BankTransfer)->placeholder('—'),
                TextEntry::make('bank_country')->visible(fn (AffiliatePaymentProfile $record): bool => $record->payment_method === AffiliatePaymentMethod::BankTransfer)->placeholder('—'),
                TextEntry::make('swift_bic')->visible(fn (AffiliatePaymentProfile $record): bool => $record->payment_method === AffiliatePaymentMethod::BankTransfer)->placeholder('—'),
                TextEntry::make('verified_at')->label('Automatically Approved At')->dateTime('d M Y H:i')->placeholder('Incomplete'),
            ])->columns(2),
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => ListAffiliatePaymentProfiles::route('/'), 'view' => ViewAffiliatePaymentProfile::route('/{record}')];
    }
}
