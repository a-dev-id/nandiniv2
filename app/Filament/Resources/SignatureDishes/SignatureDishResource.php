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
use Filament\Forms\Components\Repeater;
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
        $mainImageUpload = self::withLocalPreview(
            FileUpload::make('image')->label('Main Image')->disk('public')->directory('dining/signature-dishes')
                ->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->imagePreviewHeight('240')->panelAspectRatio('4:3')->panelLayout('integrated')->openable()->downloadable()
                ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                    file: $file, directory: 'dining/signature-dishes', targetWidth: 1200, targetHeight: 900,
                )),
        );

        $detailImageUpload = fn (
            string $field,
            string $label,
            int $width,
            int $height,
            string $ratio = '4:3',
        ): FileUpload => self::withLocalPreview(
            \App\Filament\Resources\DiningSettings\DiningSettingResource::imageUpload(
                $field,
                $field === 'image' ? 'dining/signature-dishes/components' : 'dining/signature-dishes/detail',
                $width,
                $height,
                $ratio,
            )->label($label),
        );

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
                $mainImageUpload,
                TextInput::make('image_alt')->label('Image Alt Text')->maxLength(255),
                Textarea::make('short_description')->rows(4)->columnSpanFull(),
                TextInput::make('cta_label')->label('CTA Label')->maxLength(80),
                \App\Filament\Resources\DiningSettings\DiningSettingResource::linkInput('cta_url', 'CTA URL'),
            ]),
            Section::make('Detail Page')
                ->description('Manage every content block and image on the signature dish detail page. The website styling remains consistent with the Dining subdomain.')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('detail_content')
                        ->label('Structured Detail Page')
                        ->minItems(1)
                        ->maxItems(1)
                        ->defaultItems(1)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->schema([
                            Section::make('Header Image & Introduction')->columns(2)->columnSpanFull()->schema([
                                TextInput::make('hero_title')->label('Heading')->maxLength(255),
                                Textarea::make('hero_description')->label('Description')->rows(4)->columnSpanFull(),
                                $detailImageUpload('hero_image', 'Header Image', 1920, 1080, '16:9'),
                                TextInput::make('hero_image_alt')->label('Header Image Alt Text')->maxLength(255),
                            ]),
                            Section::make('Story')->columns(2)->columnSpanFull()->schema([
                                Toggle::make('story_visible')->label('Show Section')->default(true)->columnSpanFull(),
                                TextInput::make('story_eyebrow')->label('Eyebrow')->maxLength(255),
                                TextInput::make('story_title')->label('Heading')->maxLength(255),
                                RichEditor::make('story_description')->label('Story')->columnSpanFull(),
                                $detailImageUpload('story_image', 'Story Image', 1200, 900),
                                TextInput::make('story_image_alt')->label('Story Image Alt Text')->maxLength(255),
                                Textarea::make('story_quote')->label('Quote')->rows(2)->columnSpanFull(),
                            ]),
                            Section::make('Experience Highlights')->columns(2)->columnSpanFull()->schema([
                                Toggle::make('highlights_visible')->label('Show Section')->default(true)->columnSpanFull(),
                                TextInput::make('highlights_eyebrow')->label('Eyebrow')->maxLength(255),
                                TextInput::make('highlights_title')->label('Heading')->maxLength(255),
                                Repeater::make('highlights')->columns(2)->minItems(1)->maxItems(8)->reorderable()->collapsible()
                                    ->itemLabel(fn (array $state): string => $state['title'] ?? 'Highlight')
                                    ->addActionLabel('Add highlight')->columnSpanFull()->schema([
                                        TextInput::make('title')->required()->maxLength(255),
                                        Textarea::make('description')->required()->rows(3),
                                    ]),
                            ]),
                            Section::make('Dish Components')->columns(2)->columnSpanFull()->schema([
                                Toggle::make('components_visible')->label('Show Section')->default(true)->columnSpanFull(),
                                TextInput::make('components_eyebrow')->label('Eyebrow')->maxLength(255),
                                TextInput::make('components_title')->label('Heading')->maxLength(255),
                                Textarea::make('components_description')->label('Introduction')->rows(3)->columnSpanFull(),
                                Repeater::make('components')->columns(2)->minItems(1)->maxItems(16)->reorderable()->collapsible()->cloneable()
                                    ->itemLabel(fn (array $state): string => $state['title'] ?? 'Dish component')
                                    ->addActionLabel('Add dish component')->columnSpanFull()->schema([
                                        TextInput::make('title')->required()->maxLength(255),
                                        Textarea::make('description')->required()->rows(3),
                                        $detailImageUpload('image', 'Component Image', 1000, 1000, '1:1'),
                                        TextInput::make('image_alt')->label('Image Alt Text')->required()->maxLength(255),
                                    ]),
                            ]),
                            Section::make('Premium Experience')->columns(2)->columnSpanFull()->schema([
                                Toggle::make('premium_visible')->label('Show Section')->default(true)->columnSpanFull(),
                                TextInput::make('premium_eyebrow')->label('Eyebrow')->maxLength(255),
                                TextInput::make('premium_title')->label('Heading')->maxLength(255),
                                RichEditor::make('premium_description')->label('Description')->columnSpanFull(),
                                $detailImageUpload('premium_image', 'Premium Experience Image', 1200, 900),
                                TextInput::make('premium_image_alt')->label('Image Alt Text')->maxLength(255),
                            ]),
                            Section::make('Reservation CTA')->columns(2)->columnSpanFull()->schema([
                                Toggle::make('reservation_visible')->label('Show Section')->default(true)->columnSpanFull(),
                                TextInput::make('reservation_eyebrow')->label('Eyebrow')->maxLength(255),
                                TextInput::make('reservation_title')->label('Heading')->maxLength(255),
                                Textarea::make('reservation_description')->label('Description')->rows(4)->columnSpanFull(),
                                TextInput::make('reservation_button_label')->label('Button Label')->maxLength(80),
                                \App\Filament\Resources\DiningSettings\DiningSettingResource::linkInput('reservation_button_url', 'Button URL'),
                                $detailImageUpload('reservation_image', 'Background Image', 1920, 900, '16:9'),
                                TextInput::make('reservation_image_alt')->label('Background Image Alt Text')->maxLength(255),
                            ]),
                        ]),
                    RichEditor::make('content')
                        ->label('Full Content (Legacy Fallback)')
                        ->helperText('Used only when no structured detail-page content has been configured.')
                        ->columnSpanFull(),
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

    private static function withLocalPreview(FileUpload $upload): FileUpload
    {
        return $upload
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(function (FileUpload $component, string $file, string|array|null $storedFileNames): array {
                $url = match (true) {
                    str_starts_with($file, 'http://'), str_starts_with($file, 'https://') => $file,
                    str_starts_with($file, '/') => asset($file),
                    default => asset('storage/'.$file),
                };

                return [
                    'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                    'size' => 0,
                    'type' => null,
                    'url' => $url,
                ];
            });
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
