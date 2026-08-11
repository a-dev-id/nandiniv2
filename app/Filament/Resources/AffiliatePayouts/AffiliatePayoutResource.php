<?php

namespace App\Filament\Resources\AffiliatePayouts;

use App\Enums\AffiliatePaymentMethod;
use App\Enums\AffiliatePayoutStatus;
use App\Filament\Resources\AffiliatePayouts\Pages\ListAffiliatePayouts;
use App\Filament\Resources\AffiliatePayouts\Pages\ViewAffiliatePayout;
use App\Models\AffiliatePayout;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AffiliatePayoutResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = AffiliatePayout::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static ?string $navigationLabel = 'Affiliate Payouts';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Finance';

    protected static ?int $navigationSort = 23;

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('payout_number')->searchable()->copyable(),
            TextColumn::make('affiliate.name')->searchable()->sortable(),
            TextColumn::make('currency')->sortable(),
            TextColumn::make('gross_commission_amount')->label('Gross')->state(fn (AffiliatePayout $record): string => app(AffiliateMoneyFormatter::class)->format($record->gross_commission_amount, $record->currency)),
            TextColumn::make('adjustment_amount')->label('Adjustment')->state(fn (AffiliatePayout $record): string => app(AffiliateMoneyFormatter::class)->format($record->adjustment_amount, $record->currency)),
            TextColumn::make('net_payout_amount')->label('Net')->state(fn (AffiliatePayout $record): string => app(AffiliateMoneyFormatter::class)->format($record->net_payout_amount, $record->currency)),
            TextColumn::make('payment_method_snapshot')->label('Method')->formatStateUsing(fn (string $state): string => AffiliatePaymentMethod::from($state)->label()),
            TextColumn::make('status')->badge()->formatStateUsing(fn (AffiliatePayoutStatus $state): string => $state->label()),
            TextColumn::make('due_at')->label('Due')->date('d M Y')->sortable(),
            TextColumn::make('paid_at')->label('Paid')->date('d M Y')->placeholder('—')->sortable(),
            TextColumn::make('preparer.name')->label('Prepared By')->placeholder('Automatic'),
            TextColumn::make('paidUser.name')->label('Paid By')->placeholder('—'),
        ])->filters([
            SelectFilter::make('status')->options(collect(AffiliatePayoutStatus::cases())->mapWithKeys(fn ($status): array => [$status->value => $status->label()])->all()),
            SelectFilter::make('currency')->options(fn (): array => AffiliatePayout::query()->distinct()->pluck('currency', 'currency')->all()),
            SelectFilter::make('payment_method_snapshot')->label('Payment Method')->options(collect(AffiliatePaymentMethod::cases())->mapWithKeys(fn ($method): array => [$method->value => $method->label()])->all()),
            SelectFilter::make('affiliate_id')->label('Affiliate')->relationship('affiliate', 'name')->searchable()->preload(),
            self::dateFilter('due_at', 'Due Date'),
            self::dateFilter('paid_at', 'Paid Date'),
        ])->recordUrl(fn (AffiliatePayout $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payout record')->schema([
                TextEntry::make('payout_number'), TextEntry::make('affiliate.name'), TextEntry::make('currency'),
                TextEntry::make('gross_commission_amount')->label('Gross Commission'), TextEntry::make('adjustment_amount')->label('Adjustment'), TextEntry::make('net_payout_amount')->label('Net Payout'),
                TextEntry::make('adjustment_reason')->placeholder('No adjustment')->columnSpanFull(),
                TextEntry::make('payment_method_snapshot')->label('Payment Method')->formatStateUsing(fn (string $state): string => AffiliatePaymentMethod::from($state)->label()),
                TextEntry::make('payment_details_masked_snapshot')->label('Masked Payment Details'),
                TextEntry::make('status')->badge()->formatStateUsing(fn (AffiliatePayoutStatus $state): string => $state->label()),
                TextEntry::make('due_at')->dateTime('d M Y'), TextEntry::make('processing_at')->dateTime('d M Y H:i')->placeholder('—'), TextEntry::make('paid_at')->dateTime('d M Y H:i')->placeholder('—'),
                TextEntry::make('payment_reference')->placeholder('—'), TextEntry::make('failure_reason')->placeholder('—')->columnSpanFull(), TextEntry::make('cancellation_reason')->placeholder('—')->columnSpanFull(),
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
        return ['index' => ListAffiliatePayouts::route('/'), 'view' => ViewAffiliatePayout::route('/{record}')];
    }

    private static function dateFilter(string $field, string $label): Filter
    {
        return Filter::make($field)->schema([
            DatePicker::make('from')->label($label.' From'),
            DatePicker::make('until')->label($label.' Until'),
        ])->query(fn (Builder $query, array $data): Builder => $query
            ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate($field, '>=', $date))
            ->when($data['until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate($field, '<=', $date)));
    }
}
