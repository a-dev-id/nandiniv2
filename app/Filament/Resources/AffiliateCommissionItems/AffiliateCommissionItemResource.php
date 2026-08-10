<?php

namespace App\Filament\Resources\AffiliateCommissionItems;

use App\Enums\AffiliateCommissionItemStatus;
use App\Filament\Resources\AffiliateCommissionItems\Pages\ListAffiliateCommissionItems;
use App\Filament\Resources\AffiliateCommissionItems\Pages\ViewAffiliateCommissionItem;
use App\Models\AffiliateCommissionItem;
use App\Models\Permission;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use App\Services\Affiliate\Finance\AffiliateCommissionReviewService;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class AffiliateCommissionItemResource extends Resource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $model = AffiliateCommissionItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Commissions';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 50;

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('affiliate.name')->searchable()->sortable(),
            TextColumn::make('booking.check_out_date')->label('Check-out')->date('d M Y')->sortable(),
            TextColumn::make('booking.room_types')->label('Room Type')->state(fn (AffiliateCommissionItem $record): string => $record->booking->roomTypesLabel())->wrap(),
            TextColumn::make('currency'),
            TextColumn::make('original_commission_amount')->label('Original Commission')->state(fn (AffiliateCommissionItem $record): string => app(AffiliateMoneyFormatter::class)->format($record->original_commission_amount, $record->currency)),
            TextColumn::make('approved_commission_amount')->label('Approved Amount')->state(fn (AffiliateCommissionItem $record): string => $record->approved_commission_amount === null ? '—' : app(AffiliateMoneyFormatter::class)->format($record->approved_commission_amount, $record->currency)),
            TextColumn::make('status')->badge()->formatStateUsing(fn (AffiliateCommissionItemStatus $state): string => $state->label())->color(fn (AffiliateCommissionItemStatus $state): string => $state->badgeColor()),
            IconColumn::make('source_changed_after_review')->label('Source Changed')->boolean(),
        ])->filters([
            SelectFilter::make('commission_period_id')->label('Period')->relationship('period', 'period_start_date'),
            SelectFilter::make('affiliate_id')->label('Affiliate')->relationship('affiliate', 'name')->searchable()->preload(),
            SelectFilter::make('currency')->options(fn (): array => AffiliateCommissionItem::query()->distinct()->pluck('currency', 'currency')->all()),
            SelectFilter::make('status')->options(collect(AffiliateCommissionItemStatus::cases())->mapWithKeys(fn ($status): array => [$status->value => $status->label()])->all()),
            SelectFilter::make('source_changed_after_review')->label('Source-change warning')->options([1 => 'Changed', 0 => 'Unchanged']),
            Filter::make('check_out_date')->schema([DatePicker::make('from')->label('Check-out From'), DatePicker::make('until')->label('Check-out Until')])->query(fn (Builder $query, array $data): Builder => $query->whereHas('booking', fn (Builder $booking): Builder => $booking
                ->when($data['from'] ?? null, fn (Builder $booking, $date): Builder => $booking->whereDate('check_out_date', '>=', $date))
                ->when($data['until'] ?? null, fn (Builder $booking, $date): Builder => $booking->whereDate('check_out_date', '<=', $date)))),
            Filter::make('missing_calculation')->label('Missing Calculation')->query(fn (Builder $query): Builder => $query->whereHas('booking', fn (Builder $booking): Builder => $booking->whereNotNull('calculation_unavailable_reason'))),
        ])->recordUrl(fn (AffiliateCommissionItem $record): string => static::getUrl('view', ['record' => $record]))
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveOriginalAmounts')->label('Approve Original Amounts')->requiresConfirmation()->visible(fn (): bool => auth()->user()?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_APPROVE) === true)->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            app(AffiliateCommissionReviewService::class)->approve($record, auth()->user(), $record->original_commission_amount);
                        }
                        Notification::make()->title('Selected original commission amounts approved')->success()->send();
                    }),
                    BulkAction::make('hold')->label('Hold Selected')->color('warning')->form([Textarea::make('hold_reason')->required()->maxLength(2000)])->visible(fn (): bool => auth()->user()?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE) === true)->action(function (Collection $records, array $data): void {
                        foreach ($records as $record) {
                            app(AffiliateCommissionReviewService::class)->hold($record, auth()->user(), $data['hold_reason']);
                        }
                    }),
                    BulkAction::make('exclude')->label('Exclude Selected')->color('danger')->requiresConfirmation()->form([Textarea::make('exclusion_reason')->required()->maxLength(2000)])->visible(fn (): bool => auth()->user()?->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE) === true)->action(function (Collection $records, array $data): void {
                        foreach ($records as $record) {
                            app(AffiliateCommissionReviewService::class)->exclude($record, auth()->user(), $data['exclusion_reason']);
                        }
                    }),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Qualified stay snapshot')->schema([
                TextEntry::make('affiliate.name'),
                TextEntry::make('booking.room_types')->label('Room Type')->state(fn (AffiliateCommissionItem $record): string => $record->booking->roomTypesLabel()),
                TextEntry::make('booking.check_in_date')->label('Check-in')->date('d M Y'),
                TextEntry::make('booking.check_out_date')->label('Check-out')->date('d M Y'),
                TextEntry::make('currency'),
                TextEntry::make('room_revenue_snapshot')->label('Room Revenue Snapshot'),
                TextEntry::make('commission_rate_snapshot')->label('Commission Rate')->suffix('%'),
                TextEntry::make('original_commission_amount')->label('Original Commission'),
                TextEntry::make('approved_commission_amount')->label('Approved Commission')->placeholder('Not approved'),
                TextEntry::make('status')->badge()->formatStateUsing(fn (AffiliateCommissionItemStatus $state): string => $state->label())->color(fn (AffiliateCommissionItemStatus $state): string => $state->badgeColor()),
                TextEntry::make('hold_reason')->placeholder('—')->columnSpanFull(),
                TextEntry::make('exclusion_reason')->placeholder('—')->columnSpanFull(),
                TextEntry::make('adjustment_reason')->placeholder('—')->columnSpanFull(),
                TextEntry::make('discrepancy_warning')->placeholder('None')->columnSpanFull(),
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
        return ['index' => ListAffiliateCommissionItems::route('/'), 'view' => ViewAffiliateCommissionItem::route('/{record}')];
    }
}
