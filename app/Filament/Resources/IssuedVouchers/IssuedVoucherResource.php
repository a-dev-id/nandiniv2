<?php

namespace App\Filament\Resources\IssuedVouchers;

use App\Filament\Resources\IssuedVouchers\Pages\ListIssuedVouchers;
use App\Filament\Resources\IssuedVouchers\Pages\ViewIssuedVoucher;
use App\Models\IssuedVoucher;
use App\Services\Voucher\VoucherRedemptionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
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

    protected static ?int $navigationSort = 10;

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
        ])->recordActions([
            static::redeemAction(),
            ViewAction::make(),
        ]);
    }

    public static function redeemAction(): Action
    {
        return Action::make('redeem')
            ->label('Redeem')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->visible(fn(IssuedVoucher $record): bool => static::isRedeemable($record))
            ->modalHeading(fn(IssuedVoucher $record): string => 'Redeem ' . $record->voucher_code)
            ->modalDescription('Confirm the voucher details with the guest before completing this redemption. This action cannot be undone.')
            ->fillForm(fn(IssuedVoucher $record): array => [
                'amount' => $record->remaining_value,
            ])
            ->form([
                Placeholder::make('voucher_summary')
                    ->label('Voucher')
                    ->content(fn(IssuedVoucher $record): string => $record->title . ' - ' . $record->recipient_name),
                TextInput::make('amount')
                    ->label('Redemption Amount')
                    ->prefix(fn(IssuedVoucher $record): string => $record->currency)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn(IssuedVoucher $record): int => (int) $record->remaining_value)
                    ->required()
                    ->visible(fn(IssuedVoucher $record): bool => static::allowsPartialRedemption($record))
                    ->helperText('Enter the value being used during this visit.'),
                Textarea::make('notes')
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->requiresConfirmation()
            ->modalSubmitActionLabel('Redeem Voucher')
            ->action(function (IssuedVoucher $record, array $data): void {
                app(VoucherRedemptionService::class)->redeem($record, $data, auth()->user());

                Notification::make()
                    ->title('Voucher redeemed')
                    ->body($record->voucher_code . ' was redeemed successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function isRedeemable(IssuedVoucher $voucher): bool
    {
        return in_array($voucher->status, ['active', 'partially_redeemed'], true)
            && (! $voucher->valid_from || $voucher->valid_from->isToday() || $voucher->valid_from->isPast())
            && (! $voucher->expires_at || $voucher->expires_at->isToday() || $voucher->expires_at->isFuture())
            && (int) $voucher->remaining_value > 0;
    }

    public static function allowsPartialRedemption(IssuedVoucher $voucher): bool
    {
        return (bool) data_get($voucher->orderItem?->voucher_snapshot, 'allow_partial_redemption', false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIssuedVouchers::route('/'),
            'view' => ViewIssuedVoucher::route('/{record}'),
        ];
    }
}
