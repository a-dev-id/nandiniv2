<?php

namespace App\Filament\Resources\Affiliates;

use App\Filament\Resources\Affiliates\Pages\CreateAffiliate;
use App\Filament\Resources\Affiliates\Pages\EditAffiliate;
use App\Filament\Resources\Affiliates\Pages\ListAffiliates;
use App\Filament\Resources\Affiliates\Pages\ViewAffiliate;
use App\Filament\Resources\Affiliates\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\Affiliates\RelationManagers\ClickEventsRelationManager;
use App\Filament\Resources\Affiliates\Schemas\AffiliateForm;
use App\Filament\Resources\Affiliates\Tables\AffiliatesTable;
use App\Models\Affiliate;
use App\Models\Permission;
use App\Services\Affiliate\AffiliateBookingUrlBuilder;
use App\Services\Affiliate\AffiliateLinkService;
use App\Services\Affiliate\AffiliateWorkflowService;
use App\Services\Affiliate\Booking\AffiliateBookingAnalyticsService;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Affiliates';

    protected static ?string $modelLabel = 'Affiliate';

    protected static ?string $pluralModelLabel = 'Affiliate Members';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Management';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return AffiliatesTable::configure($table);
    }

    public static function approveAction(): Action
    {
        return Action::make('approve')
            ->label('Approve Affiliate')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->visible(fn (Affiliate $record): bool => $record->isPending() && auth()->user()?->hasPermissionTo(Permission::AFFILIATE_APPROVE))
            ->requiresConfirmation()
            ->modalHeading('Approve Affiliate')
            ->modalDescription(fn (Affiliate $record): string => "Approve {$record->name} ({$record->email})? Code: {$record->affiliate_code}. Short link: ".app(AffiliateLinkService::class)->shortLink($record).'. The affiliate will gain access to active affiliate tools.')
            ->modalSubmitActionLabel('Approve Affiliate')
            ->action(function (Affiliate $record): void {
                Gate::authorize('approve', $record);
                app(AffiliateWorkflowService::class)->approve($record, auth()->user());
                Notification::make()->title('Affiliate approved')->success()->send();
            });
    }

    public static function rejectAction(): Action
    {
        return Action::make('reject')
            ->label('Reject Affiliate')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->visible(fn (Affiliate $record): bool => $record->isPending() && auth()->user()?->hasPermissionTo(Permission::AFFILIATE_REJECT))
            ->form([
                Placeholder::make('affiliate')->content(fn (Affiliate $record): string => $record->name.' · '.$record->email.' · '.$record->affiliate_code),
                Textarea::make('reason')->label('Affiliate-visible reason')->required()->maxLength(2000)->rows(4),
            ])
            ->requiresConfirmation()
            ->modalHeading('Reject Affiliate')
            ->modalSubmitActionLabel('Reject Affiliate')
            ->action(function (Affiliate $record, array $data): void {
                Gate::authorize('reject', $record);
                app(AffiliateWorkflowService::class)->reject($record, auth()->user(), $data['reason']);
                Notification::make()->title('Affiliate rejected')->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAffiliates::route('/'),
            'create' => CreateAffiliate::route('/create'),
            'view' => ViewAffiliate::route('/{record}'),
            'edit' => EditAffiliate::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [BookingsRelationManager::class, ClickEventsRelationManager::class];
    }

    public static function form(Schema $schema): Schema
    {
        return AffiliateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')->schema([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('phone_whatsapp')->label('Phone / WhatsApp'),
                TextEntry::make('status')->badge()->formatStateUsing(fn ($state): string => $state->label()),
                TextEntry::make('registration_source')->label('Registration Source')->formatStateUsing(fn ($state): string => $state->label()),
                TextEntry::make('created_at')->label('Created')->dateTime('d M Y H:i'),
                TextEntry::make('creator.name')->label('Created By')->placeholder('Self registration'),
            ])->columns(2),
            Section::make('Social profiles')->schema(collect(['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'x' => 'X', 'threads' => 'Threads'])
                ->map(fn (string $label, string $field): TextEntry => TextEntry::make($field)
                    ->label($label)
                    ->visible(fn (Affiliate $record): bool => filled($record->{$field}))
                    ->url(fn (?string $state): ?string => filter_var($state, FILTER_VALIDATE_URL) ? $state : null, shouldOpenInNewTab: true))
                ->all())->columns(2),
            Section::make('Affiliate information')->schema([
                TextEntry::make('affiliate_code')->label('Affiliate Code')->helperText(fn (Affiliate $record): ?string => $record->isPending() ? 'Hidden from affiliate until approval' : null),
                TextEntry::make('short_link')->label('Short Link')->state(fn (Affiliate $record): string => filled($record->short_link_slug) ? app(AffiliateLinkService::class)->shortLink($record) : 'Unavailable')->url(fn (Affiliate $record): ?string => filled($record->short_link_slug) ? app(AffiliateLinkService::class)->shortLink($record) : null, shouldOpenInNewTab: true),
                TextEntry::make('booking_link')->label('Full Booking-engine URL')->state(fn (Affiliate $record): string => filled($record->affiliate_code) ? app(AffiliateBookingUrlBuilder::class)->build($record->affiliate_code) : 'Unavailable')->url(fn (Affiliate $record): ?string => filled($record->affiliate_code) ? app(AffiliateBookingUrlBuilder::class)->build($record->affiliate_code) : null, shouldOpenInNewTab: true)->columnSpanFull(),
                TextEntry::make('short_link_activated_at')->label('Short-link Activation')->formatStateUsing(fn ($state): string => $state ? 'Active since '.$state->format('d M Y H:i') : 'Inactive'),
                TextEntry::make('approved_at')->label('Approved')->dateTime('d M Y H:i')->placeholder('-'),
                TextEntry::make('approver.name')->label('Approved By')->placeholder('-'),
                TextEntry::make('rejection_reason')->label('Affiliate-visible Rejection Reason')->visible(fn (Affiliate $record): bool => filled($record->rejection_reason))->columnSpanFull(),
            ])->columns(2),
            Section::make('Click analytics')->schema([
                TextEntry::make('click_total')->label('Total non-bot clicks')->state(fn (Affiliate $record): int => app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['total']),
                TextEntry::make('click_unique')->label('Total unique clicks')->state(fn (Affiliate $record): int => app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['unique']),
                TextEntry::make('click_month')->label('Clicks this month')->state(fn (Affiliate $record): int => app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['this_month']),
                TextEntry::make('click_top_country')->label('Top country')->state(fn (Affiliate $record): string => app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['top_country'] ?: 'No country data yet'),
                TextEntry::make('click_top_device')->label('Top device')->state(fn (Affiliate $record): string => ucfirst(app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['top_device'] ?: 'unknown')),
                TextEntry::make('click_last')->label('Last click')->state(fn (Affiliate $record): ?string => app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['last_click'])->dateTime('d M Y H:i')->placeholder('No clicks yet'),
                TextEntry::make('click_bots')->label('Bot or preview clicks')->state(fn (Affiliate $record): int => app(AffiliateClickAnalyticsService::class)->forAffiliate($record, 'all')['summary']['bots']),
            ])->columns(2)->visible(fn (): bool => auth('web')->user()?->hasPermissionTo(Permission::AFFILIATE_CLICK_VIEW) === true),
            Section::make('Booking summary')->schema([
                TextEntry::make('booking_total')->label('Tracked bookings')->state(fn (Affiliate $record): int => app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($record)['tracked_bookings']),
                TextEntry::make('booking_upcoming')->label('Upcoming stays')->state(fn (Affiliate $record): int => app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($record)['upcoming_stays']),
                TextEntry::make('booking_completed')->label('Completed stays')->state(fn (Affiliate $record): int => app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($record)['completed_stays']),
                TextEntry::make('booking_room_nights')->label('Room nights')->state(fn (Affiliate $record): int => app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($record)['room_nights']),
                TextEntry::make('booking_commission')->label('Estimated commission by currency')->state(fn (Affiliate $record): array => collect(app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($record)['commission_totals'])->map(fn (array $total): string => app(AffiliateMoneyFormatter::class)->format($total['amount'], $total['currency']))->all())->listWithLineBreaks()->placeholder('Pending calculation'),
                TextEntry::make('booking_last_sync')->label('Last booking synchronization')->state(fn (Affiliate $record) => app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($record)['last_synced_at'])->dateTime('d M Y H:i')->placeholder('No bookings synchronized'),
            ])->columns(2)->visible(fn (): bool => auth('web')->user()?->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW) === true),
        ]);
    }
}
