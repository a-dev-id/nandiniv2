<?php

namespace App\Filament\Resources\VoucherOrders;

use App\Filament\Resources\VoucherOrders\Pages\ListVoucherOrders;
use App\Filament\Resources\VoucherOrders\Pages\ViewVoucherOrder;
use App\Models\VoucherOrder;
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

class VoucherOrderResource extends Resource
{
    protected static ?string $model = VoucherOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    protected static ?int $navigationSort = 80;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('order_number')->content(fn($record) => $record?->order_number),
            Placeholder::make('purchaser')->content(fn($record) => $record ? trim($record->purchaser_first_name . ' ' . $record->purchaser_last_name) . ' <' . $record->purchaser_email . '>' : null),
            Placeholder::make('payment_status')->content(fn($record) => $record?->payment_status),
            Placeholder::make('flywire')->content(fn($record) => $record?->flywire_payment_reference ?: $record?->flywire_payment_id ?: '-'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order_number')->searchable()->sortable(),
            TextColumn::make('purchaser_email')->searchable(),
            TextColumn::make('member.email')->label('Member')->placeholder('Guest'),
            TextColumn::make('total_amount')->money('IDR', divideBy: 1)->sortable(),
            TextColumn::make('payment_status')->badge(),
            TextColumn::make('order_status')->badge(),
            TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('payment_status')->options([
                'pending' => 'Pending',
                'payment_session_created' => 'Payment session created',
                'processing' => 'Processing',
                'paid' => 'Paid',
                'failed' => 'Failed',
                'cancelled' => 'Cancelled',
            ]),
        ])->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoucherOrders::route('/'),
            'view' => ViewVoucherOrder::route('/{record}'),
        ];
    }
}
