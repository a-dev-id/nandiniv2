<?php

namespace App\Filament\Resources\VoucherRedemptions;

use App\Filament\Resources\VoucherRedemptions\Pages\ListVoucherRedemptions;
use App\Filament\Resources\VoucherRedemptions\Pages\ViewVoucherRedemption;
use App\Models\VoucherRedemption;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VoucherRedemptionResource extends Resource
{
    protected static ?string $model = VoucherRedemption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('voucher')->content(fn($record) => $record?->issuedVoucher?->voucher_code),
            Placeholder::make('amount')->content(fn($record) => $record?->amount),
            Placeholder::make('department')->content(fn($record) => $record?->department ?: '-'),
            Placeholder::make('notes')->content(fn($record) => $record?->notes ?: '-'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('issuedVoucher.voucher_code')->label('Voucher')->searchable(),
            TextColumn::make('department')->searchable(),
            TextColumn::make('reference_number')->searchable(),
            TextColumn::make('amount')->money('IDR', divideBy: 1),
            TextColumn::make('redeemed_at')->dateTime('d M Y H:i')->sortable(),
        ])->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoucherRedemptions::route('/'),
            'view' => ViewVoucherRedemption::route('/{record}'),
        ];
    }
}
