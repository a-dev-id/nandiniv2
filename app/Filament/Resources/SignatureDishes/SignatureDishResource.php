<?php

namespace App\Filament\Resources\SignatureDishes;

use App\Filament\Resources\SignatureDishes\Pages\CreateSignatureDish;
use App\Filament\Resources\SignatureDishes\Pages\EditSignatureDish;
use App\Filament\Resources\SignatureDishes\Pages\ListSignatureDishes;
use App\Filament\Resources\SignatureDishes\RelationManagers\SectionsRelationManager;
use App\Models\SignatureDish;
use App\Support\FilamentWebpUpload;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class SignatureDishResource extends Resource
{
    protected static ?string $model = SignatureDish::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;
    protected static string|UnitEnum|null $navigationGroup = 'Dining Landing Page';
    protected static ?string $navigationLabel = 'Signature Dish';
    protected static ?int $navigationSort = 45;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(12)->components([
            Section::make('Basic Information')->columns(2)->columnSpanFull()->schema([
                TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('eyebrow')->maxLength(255),
                TextInput::make('subtitle')->label('Subtitle')->maxLength(255),
                TextInput::make('price')->maxLength(255)->helperText('Optional. Include the price in the title when it should read as one heading.'),
            ]),
            Section::make('Landing Page')->columns(2)->columnSpanFull()->schema([
                FileUpload::make('image')->label('Main Image')->disk('public')->directory('dining/signature-dishes')
                    ->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imagePreviewHeight('240')->panelAspectRatio('4:3')->panelLayout('integrated')->openable()->downloadable()
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                        file: $file, directory: 'dining/signature-dishes', targetWidth: 1200, targetHeight: 900,
                    )),
                TextInput::make('image_alt')->label('Image Alt Text')->maxLength(255),
                Textarea::make('short_description')->rows(4)->columnSpanFull(),
                TextInput::make('cta_label')->label('CTA Label')->maxLength(80),
                \App\Filament\Resources\DiningSettings\DiningSettingResource::linkInput('cta_url', 'CTA URL'),
            ]),
            Section::make('Detail Page')->columnSpanFull()->schema([
                RichEditor::make('content')->label('Full Content')->columnSpanFull(),
            ]),
            Section::make('Publishing')->columns(2)->columnSpanFull()->schema([
                Toggle::make('is_published')->label('Published')->default(true),
                TextInput::make('sort_order')->numeric()->minValue(1),
            ]),
            Section::make('SEO')->columns(2)->columnSpanFull()->schema([
                TextInput::make('meta_title')->maxLength(70),
                Textarea::make('meta_description')->maxLength(180)->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->columns([
            ImageColumn::make('image')->disk('public')->label('Image')->width(80)->height(60),
            TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
            TextColumn::make('price'),
            ToggleColumn::make('is_published')->label('Published'),
            TextColumn::make('sort_order')->label('Sort Order')->sortable(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSignatureDishes::route('/'),
            'create' => CreateSignatureDish::route('/create'),
            'edit' => EditSignatureDish::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [SectionsRelationManager::class];
    }
}
