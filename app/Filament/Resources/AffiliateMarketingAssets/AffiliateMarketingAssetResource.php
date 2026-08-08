<?php

namespace App\Filament\Resources\AffiliateMarketingAssets;

use App\Enums\AffiliateMarketingAssetType;
use App\Filament\Resources\AffiliateMarketingAssets\Pages\CreateAffiliateMarketingAsset;
use App\Filament\Resources\AffiliateMarketingAssets\Pages\EditAffiliateMarketingAsset;
use App\Filament\Resources\AffiliateMarketingAssets\Pages\ListAffiliateMarketingAssets;
use App\Models\AffiliateMarketingAsset;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AffiliateMarketingAssetResource extends Resource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $model = AffiliateMarketingAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Marketing Assets';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate Management';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Material')->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Select::make('asset_type')
                    ->options(collect(AffiliateMarketingAssetType::cases())->mapWithKeys(fn ($type): array => [$type->value => $type->label()])->all())
                    ->required()->live(),
                TextInput::make('sort_order')->integer()->minValue(0)->default(0)->required(),
                Textarea::make('description')->rows(4)->maxLength(2000)->columnSpanFull(),
                FileUpload::make('file_path')
                    ->label('Protected File')
                    ->disk('local')->directory('affiliate/marketing-assets')
                    ->storeFileNamesIn('file_name')
                    ->acceptedFileTypes(fn (Get $get): array => match ($get('asset_type')) {
                        AffiliateMarketingAssetType::Document->value => ['application/pdf'],
                        AffiliateMarketingAssetType::Video->value => [],
                        AffiliateMarketingAssetType::Image->value, AffiliateMarketingAssetType::Banner->value, AffiliateMarketingAssetType::SocialMedia->value => ['image/jpeg', 'image/png', 'image/webp'],
                        default => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
                    })
                    ->maxSize(10240)
                    ->helperText('Images: JPG, PNG, WEBP. Documents: PDF. Maximum 10 MB. Videos must use an approved external URL.')
                    ->disabled(fn (Get $get): bool => $get('asset_type') === AffiliateMarketingAssetType::Video->value)
                    ->required(fn (Get $get): bool => blank($get('external_url')))
                    ->downloadable(),
                FileUpload::make('thumbnail_path')
                    ->label('Optional Preview Image')
                    ->disk('local')->directory('affiliate/marketing-thumbnails')
                    ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(4096),
                TextInput::make('external_url')
                    ->label('Approved External URL')->url()->startsWith('https://')->maxLength(2048)
                    ->required(fn (Get $get): bool => blank($get('file_path')))
                    ->helperText('Use HTTPS for video hosting or externally managed materials.')
                    ->columnSpanFull(),
            ]),
            Section::make('Visibility')->columns(2)->schema([
                Toggle::make('is_active')->default(false),
                Toggle::make('is_featured')->default(false),
                DateTimePicker::make('available_from')->native(false)->beforeOrEqual('available_until'),
                DateTimePicker::make('available_until')->native(false)->afterOrEqual('available_from'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            TextColumn::make('title')->searchable()->weight('semibold'),
            TextColumn::make('asset_type')->label('Type')->badge()->formatStateUsing(fn (AffiliateMarketingAssetType $state): string => $state->label()),
            TextColumn::make('file_name')->label('File or Link')->formatStateUsing(fn (?string $state, AffiliateMarketingAsset $record): string => $state ?: ($record->external_url ? 'External link' : 'Unavailable'))->limit(32),
            IconColumn::make('is_active')->label('Active')->boolean(),
            IconColumn::make('is_featured')->label('Featured')->boolean(),
            TextColumn::make('availability')->state(fn (AffiliateMarketingAsset $record): string => match (true) {
                ! $record->is_active => 'Inactive',
                $record->available_until?->isPast() === true => 'Expired',
                $record->available_from?->isFuture() === true => 'Scheduled',
                $record->isAvailable() => 'Available',
                default => 'Unavailable',
            })->badge(),
            TextColumn::make('sort_order')->label('Order')->sortable(),
            TextColumn::make('updated_at')->label('Updated')->dateTime('d M Y H:i')->sortable(),
            TextColumn::make('updater.name')->label('Updated By')->placeholder('System'),
        ])->filters([
            SelectFilter::make('asset_type')->options(collect(AffiliateMarketingAssetType::cases())->mapWithKeys(fn ($type): array => [$type->value => $type->label()])->all()),
            TernaryFilter::make('is_active')->label('Active Status'),
            Filter::make('availability')->schema([
                Select::make('state')->options([
                    'available' => 'Available now',
                    'scheduled' => 'Scheduled',
                    'expired' => 'Expired',
                    'inactive' => 'Inactive',
                ]),
            ])->query(fn (Builder $query, array $data): Builder => match ($data['state'] ?? null) {
                'available' => $query->available(),
                'scheduled' => $query->where('is_active', true)->where('available_from', '>', now()),
                'expired' => $query->whereNotNull('available_until')->where('available_until', '<', now()),
                'inactive' => $query->where('is_active', false),
                default => $query,
            }),
        ])->recordActions([
            Action::make('activate')->icon(Heroicon::OutlinedCheckCircle)->color('success')->requiresConfirmation()->visible(fn (AffiliateMarketingAsset $record): bool => ! $record->is_active)->action(fn (AffiliateMarketingAsset $record) => $record->update(['is_active' => true])),
            Action::make('deactivate')->icon(Heroicon::OutlinedNoSymbol)->color('warning')->requiresConfirmation()->visible(fn (AffiliateMarketingAsset $record): bool => $record->is_active)->action(fn (AffiliateMarketingAsset $record) => $record->update(['is_active' => false])),
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAffiliateMarketingAssets::route('/'),
            'create' => CreateAffiliateMarketingAsset::route('/create'),
            'edit' => EditAffiliateMarketingAsset::route('/{record}/edit'),
        ];
    }
}
