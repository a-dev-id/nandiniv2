<?php

namespace App\Filament\Resources\IssuedVouchers;

use App\Filament\Resources\IssuedVouchers\Pages\ListIssuedVouchers;
use App\Filament\Resources\IssuedVouchers\Pages\ViewIssuedVoucher;
use App\Models\IssuedVoucher;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class IssuedVoucherResource extends Resource
{
    protected static ?string $model = IssuedVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('voucher_code')->content(fn($record) => $record?->voucher_code),
            Placeholder::make('title')->content(fn($record) => $record?->title),
            Placeholder::make('recipient')->content(fn($record) => $record ? $record->recipient_name . ' <' . $record->recipient_email . '>' : null),
            Placeholder::make('status')->content(fn($record) => $record?->status),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('voucher_code')->searchable()->sortable(),
            TextColumn::make('title')->searchable(),
            TextColumn::make('recipient_email')->searchable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('remaining_value')->money('IDR', divideBy: 1),
            TextColumn::make('expires_at')->date('d M Y')->sortable(),
        ])->filters([
            SelectFilter::make('status')->options([
                'pending' => 'Pending',
                'active' => 'Active',
                'partially_redeemed' => 'Partially redeemed',
                'redeemed' => 'Redeemed',
                'expired' => 'Expired',
                'cancelled' => 'Cancelled',
                'voided' => 'Voided',
            ]),
        ])->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIssuedVouchers::route('/'),
            'view' => ViewIssuedVoucher::route('/{record}'),
        ];
    }
}
