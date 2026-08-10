<?php

namespace App\Filament\Resources\AffiliateProgramSettings;

use App\Filament\Resources\AffiliatePayoutMinimums\AffiliatePayoutMinimumResource;
use App\Filament\Resources\AffiliateProgramSettings\Pages\EditAffiliateProgramSetting;
use App\Filament\Resources\AffiliateProgramSettings\Pages\ListAffiliateProgramSettings;
use App\Models\AffiliateOperationalState;
use App\Models\AffiliateProgramSetting;
use App\Models\BookingSyncLog;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class AffiliateProgramSettingResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = AffiliateProgramSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Program Settings';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate System';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('General program')->description('Domain routing is controlled by the production environment and is shown read-only here.')->schema([
                TextInput::make('program_name')->required()->maxLength(120),
                TextInput::make('affiliate_domain')->formatStateUsing(fn (): string => (string) config('domains.affiliate'))->disabled()->dehydrated(false),
                TextInput::make('short_link_domain')->formatStateUsing(fn (): string => (string) config('domains.short_link'))->disabled()->dehydrated(false),
                TextInput::make('booking_engine_base_url')->url()->required()->maxLength(2048),
                Textarea::make('review_time_message')->label('Account Review Message')->required()->maxLength(1000)->columnSpanFull(),
                TextInput::make('review_time_expectation_hours')->label('Review-Time Expectation (hours)')->integer()->required()->minValue(1)->maxValue(720),
            ])->columns(2),
            Section::make('Commercial settings')->description('Changes apply to future records only. Existing booking, commission, and payout snapshots are not rewritten.')->schema([
                TextInput::make('affiliate_commission_percentage')->label('Default Affiliate Commission Percentage')->numeric()->required()->minValue(0)->maxValue(100)->step(0.01)->suffix('%'),
                TextInput::make('guest_discount_percentage')->label('Guest Discount Percentage')->numeric()->required()->minValue(0)->maxValue(100)->step(0.01)->suffix('%'),
                Select::make('payment_cycle')->options(['monthly' => 'Monthly'])->required(),
                TextInput::make('commission_validation_start_day')->label('Validation Start Day')->integer()->required()->minValue(1)->maxValue(28),
                TextInput::make('commission_validation_end_day')->label('Validation End Day')->integer()->required()->minValue(1)->maxValue(28)->gte('commission_validation_start_day'),
                TextInput::make('payout_release_days')->integer()->required()->minValue(1)->maxValue(365),
                Placeholder::make('payout_minimums_link')
                    ->label('Per-currency payout minimums')
                    ->content(fn (): HtmlString => new HtmlString('<a class="font-medium text-primary-600 hover:underline" href="'.e(AffiliatePayoutMinimumResource::getUrl()).'">Manage payout minimums</a>'))
                    ->columnSpanFull(),
            ])->columns(3),
            Section::make('Click analytics')->schema([
                TextInput::make('click_unique_window')->formatStateUsing(fn (): string => 'daily')->disabled()->dehydrated(false)->helperText('Daily uniqueness is fixed by the current implementation.'),
                TextInput::make('click_event_retention_days')->integer()->required()->minValue(1)->maxValue(3650),
                TextInput::make('country_header')->formatStateUsing(fn (): string => config('affiliate-clicks.country_header') ?: 'Not configured')->disabled()->dehydrated(false),
                TextInput::make('geoip_availability')->formatStateUsing(fn (): string => config('affiliate-clicks.geoip_database') ? 'Configured path' : 'Not configured')->disabled()->dehydrated(false),
            ])->columns(2),
            Section::make('Booking integration status')->description('The source endpoint and credentials remain environment-controlled. No guest payload or authentication header is shown.')->schema([
                TextInput::make('booking_source_status')->label('Booking Source')->formatStateUsing(fn (): string => config('services.membership_api.url') ? 'Environment-controlled membership API' : 'Not configured')->disabled()->dehydrated(false),
                TextInput::make('last_booking_sync')->label('Last Successful Sync')->formatStateUsing(fn (): string => BookingSyncLog::query()->where('status', BookingSyncLog::STATUS_SUCCESS)->latest('finished_at')->first()?->finished_at?->format('d M Y H:i:s T') ?? 'No successful sync recorded')->disabled()->dehydrated(false),
                TextInput::make('last_booking_sync_result')->label('Last Sync Result')->formatStateUsing(fn (): string => BookingSyncLog::query()->latest('started_at')->value('status') ?? 'Unknown')->disabled()->dehydrated(false),
                TextInput::make('last_booking_sync_error')->label('Last Sync Error Class')->formatStateUsing(fn (): string => AffiliateOperationalState::query()->find('booking_sync')?->metadata['error_class'] ?? 'None recorded')->disabled()->dehydrated(false),
                TextInput::make('voucher_field_availability')->label('Voucher Field Availability')->formatStateUsing(fn (): string => (AffiliateOperationalState::query()->find('booking_sync')?->metadata['voucher_field_detected'] ?? false) ? 'Detected in last sync response' : 'Not yet detected')->disabled()->dehydrated(false),
            ])->columns(2),
            Section::make('Notifications')->schema([
                Toggle::make('registration_confirmation_enabled')->label('Registration Confirmation'),
                Toggle::make('internal_invitation_enabled')->label('Internal Invitation'),
                Toggle::make('approval_notification_enabled')->label('Approval Notification'),
                Toggle::make('rejection_notification_enabled')->label('Rejection Notification'),
                Toggle::make('payout_paid_notification_enabled')->label('Payout Paid'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program_name'), TextColumn::make('affiliate_commission_percentage')->label('Commission')->suffix('%'), TextColumn::make('guest_discount_percentage')->label('Guest Discount')->suffix('%'), TextColumn::make('commission_validation_start_day')->label('Validation Start'), TextColumn::make('commission_validation_end_day')->label('Validation End'), TextColumn::make('payout_release_days')->label('Payout Release Days'),
        ])->recordUrl(fn (AffiliateProgramSetting $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => ListAffiliateProgramSettings::route('/'), 'edit' => EditAffiliateProgramSetting::route('/{record}/edit')];
    }
}
