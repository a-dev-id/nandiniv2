<?php

namespace App\Filament\Resources\VoucherPaymentEvents;

use App\Filament\Resources\VoucherPaymentEvents\Pages\ListVoucherPaymentEvents;
use App\Filament\Resources\VoucherPaymentEvents\Pages\ViewVoucherPaymentEvent;
use App\Models\VoucherPaymentEvent;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VoucherPaymentEventResource extends Resource
{
    protected static ?string $model = VoucherPaymentEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?string $navigationLabel = 'Payment Log';

    protected static ?string $modelLabel = 'Payment Log Entry';

    protected static ?string $pluralModelLabel = 'Payment Log';

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('event_fingerprint')->content(fn($record) => $record?->event_fingerprint),
            Placeholder::make('processing_error')->content(fn($record) => $record?->processing_error ?: '-'),
            KeyValue::make('payload')->disabled()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('order.order_number')->label('Order')->searchable(),
            TextColumn::make('gateway_payment_id')->searchable(),
            TextColumn::make('event_type')->searchable(),
            TextColumn::make('gateway_status')->badge(),
            IconColumn::make('signature_valid')->boolean(),
            TextColumn::make('processed_at')->dateTime('d M Y H:i')->sortable(),
        ])->recordActions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoucherPaymentEvents::route('/'),
            'view' => ViewVoucherPaymentEvent::route('/{record}'),
        ];
    }
}
