<?php

namespace App\Filament\Resources\AffiliateCommissionPeriods;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Filament\Resources\AffiliateCommissionPeriods\Pages\ListAffiliateCommissionPeriods;
use App\Filament\Resources\AffiliateCommissionPeriods\Pages\ViewAffiliateCommissionPeriod;
use App\Models\AffiliateCommissionPeriod;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AffiliateCommissionPeriodResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = AffiliateCommissionPeriod::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Commission Periods';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Finance';

    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return $table->defaultSort('period_start_date', 'desc')->columns([
            TextColumn::make('period')->label('Period')->state(fn (AffiliateCommissionPeriod $record): string => $record->label())->searchable(['period_year', 'period_month']),
            TextColumn::make('status')->badge()->formatStateUsing(fn (AffiliateCommissionPeriodStatus $state): string => $state->label()),
            TextColumn::make('items_count')->label('Total Items')->counts('items'),
            TextColumn::make('pending_count')->label('Pending Review')->state(fn (AffiliateCommissionPeriod $record): int => $record->items()->where('status', AffiliateCommissionItemStatus::PendingReview)->count()),
            TextColumn::make('held_count')->label('Held')->state(fn (AffiliateCommissionPeriod $record): int => $record->items()->where('status', AffiliateCommissionItemStatus::Held)->count()),
            TextColumn::make('prepared_at')->dateTime('d M Y H:i')->placeholder('Not prepared'),
            TextColumn::make('finalized_at')->dateTime('d M Y H:i')->placeholder('Open'),
        ])->filters([
            SelectFilter::make('status')->options(collect(AffiliateCommissionPeriodStatus::cases())->mapWithKeys(fn ($status): array => [$status->value => $status->label()])->all()),
        ])->recordUrl(fn (AffiliateCommissionPeriod $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Validation period')->schema([
                TextEntry::make('period')->state(fn (AffiliateCommissionPeriod $record): string => $record->label()),
                TextEntry::make('status')->badge()->formatStateUsing(fn (AffiliateCommissionPeriodStatus $state): string => $state->label()),
                TextEntry::make('period_start_date')->date('d M Y'),
                TextEntry::make('period_end_date')->date('d M Y'),
                TextEntry::make('prepared_at')->dateTime('d M Y H:i')->placeholder('Not prepared'),
                TextEntry::make('preparer.name')->label('Prepared By')->placeholder('Scheduled command'),
                TextEntry::make('finalized_at')->dateTime('d M Y H:i')->placeholder('Not finalized'),
                TextEntry::make('finalizer.name')->label('Finalized By')->placeholder('—'),
                TextEntry::make('notes')->columnSpanFull()->placeholder('No notes'),
                TextEntry::make('total_items')->label('Total Items')->state(fn (AffiliateCommissionPeriod $record): int => $record->items()->count()),
                TextEntry::make('pending_items')->label('Pending Review')->state(fn (AffiliateCommissionPeriod $record): int => $record->items()->where('status', AffiliateCommissionItemStatus::PendingReview)->count()),
                TextEntry::make('held_items')->label('Held')->state(fn (AffiliateCommissionPeriod $record): int => $record->items()->where('status', AffiliateCommissionItemStatus::Held)->count()),
                TextEntry::make('excluded_items')->label('Excluded')->state(fn (AffiliateCommissionPeriod $record): int => $record->items()->where('status', AffiliateCommissionItemStatus::Excluded)->count()),
                TextEntry::make('approved_totals')->label('Approved by Currency')->state(fn (AffiliateCommissionPeriod $record): string => $record->items()->whereIn('status', [AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::IncludedInPayout, AffiliateCommissionItemStatus::Paid])->selectRaw('currency, SUM(approved_commission_amount) total')->groupBy('currency')->get()->map(fn ($row): string => app(AffiliateMoneyFormatter::class)->format($row->getRawOriginal('total'), $row->currency))->implode(' · ') ?: 'No approved amount')->columnSpanFull(),
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
        return ['index' => ListAffiliateCommissionPeriods::route('/'), 'view' => ViewAffiliateCommissionPeriod::route('/{record}')];
    }
}
