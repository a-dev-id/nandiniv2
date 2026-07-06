<?php

namespace App\Filament\Resources\BlogNews\RelationManagers;

use App\Support\FilamentWebpUpload;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Content Sections';

    private const MEDIA_SECTION_KEYS = [
        'image_overlay_section',
        'contained_image_section',
        'split_media_section',
        'split_media_reverse',
        'three_images_section',
        'two_images_section',
        'two_images_reverse',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Section Content')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->columns(2)
                    ->schema([
                        Select::make('section_key')
                            ->label('Section Type')
                            ->required()
                            ->native(false)
                            ->live()
                            ->options([
                                'intro_text_section' => 'Intro / Text Section',
                                'image_overlay_section' => 'Image Overlay Section',
                                'contained_image_section' => 'Contained Image Section',
                                'split_media_section' => 'Split Media Section',
                                'split_media_reverse' => 'Split Media Reverse',
                                'three_images_section' => 'Three Images Section',
                                'two_images_section' => 'Two Images Section',
                                'two_images_reverse' => 'Two Images Reverse',
                                'video_text_section' => 'Video Text Section',
                            ])
                            ->default('split_media_section'),

                        TextInput::make('title')
                            ->label('Title')
                            ->placeholder('Section title')
                            ->maxLength(255),

                        TextInput::make('subtitle')
                            ->label('Subtitle')
                            ->placeholder('Section subtitle')
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Description')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),

                        Section::make('Video')
                            ->visible(fn(Get $get): bool => $get('section_key') === 'video_text_section')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('video_url')
                                    ->label('Video URL')
                                    ->placeholder('Example: YouTube, Vimeo, or MP4 URL')
                                    ->maxLength(500),

                                TextInput::make('video_label')
                                    ->label('Video Label')
                                    ->placeholder('Example: Watch Video')
                                    ->maxLength(255),
                            ]),

                        Repeater::make('images')
                            ->label('Section Images')
                            ->relationship('images')
                            ->orderColumn('sort_order')
                            ->addActionLabel('Add image')
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull()
                            ->columns(2)
                            ->itemLabel(fn(array $state): string => $state['image_alt'] ?? $state['caption'] ?? 'Section Image')
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true))
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Desktop Image')
                                    ->disk('public')
                                    ->directory('blog-news/sections')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('16:9')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::store(
                                            file: $file,
                                            directory: 'blog-news/sections',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                            fileName: $get('image_file_name'),
                                        )
                                    ),

                                TextInput::make('image_file_name')
                                    ->label('Desktop Image File Name')
                                    ->placeholder('example-section-image')
                                    ->helperText('Optional. Saved as .webp; leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),

                                TextInput::make('image_alt')
                                    ->label('Desktop Alt Text')
                                    ->placeholder('Describe the desktop image')
                                    ->maxLength(255),

                                FileUpload::make('mobile_image')
                                    ->label('Mobile Image')
                                    ->disk('public')
                                    ->directory('blog-news/sections/mobile')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('4:3')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::store(
                                            file: $file,
                                            directory: 'blog-news/sections/mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                            fileName: $get('mobile_image_file_name'),
                                        )
                                    ),

                                TextInput::make('mobile_image_file_name')
                                    ->label('Mobile Image File Name')
                                    ->placeholder('example-section-mobile')
                                    ->helperText('Optional. Saved as .webp; leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),

                                TextInput::make('mobile_image_alt')
                                    ->label('Mobile Alt Text')
                                    ->placeholder('Describe the mobile image')
                                    ->maxLength(255),

                                TextInput::make('caption')
                                    ->label('Caption')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Hidden::make('is_active')
                                    ->default(true),

                                Hidden::make('sort_order')
                                    ->default(0),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Button')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Example: Discover')
                                    ->maxLength(255),

                                Select::make('button_link_type')
                                    ->label('Button Link Type')
                                    ->native(false)
                                    ->live()
                                    ->options([
                                        'manual' => 'Manual Link',
                                        'route' => 'Route',
                                    ])
                                    ->default('manual')
                                    ->required(),

                                TextInput::make('button_url')
                                    ->label('Manual Button URL')
                                    ->placeholder('Example: /blog or https://example.com')
                                    ->maxLength(500)
                                    ->visible(fn(Get $get): bool => $get('button_link_type') === 'manual'),

                                TextInput::make('button_route')
                                    ->label('Button Route')
                                    ->placeholder('Example: blog-news.index')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('button_link_type') === 'route'),
                            ]),

                        Section::make('Layout')
                            ->columnSpanFull()
                            ->schema([
                                Select::make('text_align')
                                    ->label('Text Align')
                                    ->native(false)
                                    ->options([
                                        'left' => 'Left',
                                        'center' => 'Center',
                                        'right' => 'Right',
                                    ])
                                    ->default('center')
                                    ->required(),

                                Select::make('background_color')
                                    ->label('Background Color')
                                    ->native(false)
                                    ->options([
                                        'white' => 'White',
                                        'soft_gray' => 'Soft Gray',
                                        'warm_ivory' => 'Warm Ivory',
                                        'light_gold' => 'Light Gold',
                                        'dark_navy' => 'Dark Navy',
                                    ])
                                    ->default('white'),
                            ]),

                        Section::make('Settings')
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->required(),

                                Hidden::make('sort_order')
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done sorting' : 'Sort sections')
            )
            ->columns([
                ImageColumn::make('first_image')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->getStateUsing(fn($record): ?string => $record->images()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->first()?->image),

                TextColumn::make('title')
                    ->label('Section')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(45)
                    ->placeholder('Untitled section')
                    ->description(fn($record): ?string => Str::limit($record->subtitle ?? '', 50)),

                TextColumn::make('section_key')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'intro_text_section' => 'Intro Text',
                        'image_overlay_section' => 'Image Overlay',
                        'contained_image_section' => 'Contained Image',
                        'split_media_section' => 'Split Media',
                        'split_media_reverse' => 'Split Media Reverse',
                        'three_images_section' => 'Three Images',
                        'two_images_section' => 'Two Images',
                        'two_images_reverse' => 'Two Images Reverse',
                        'video_text_section' => 'Video Text',
                        default => $state ? Str::headline($state) : '-',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'intro_text_section' => 'gray',
                        'image_overlay_section' => 'info',
                        'contained_image_section' => 'info',
                        'split_media_section' => 'success',
                        'split_media_reverse' => 'warning',
                        'three_images_section' => 'primary',
                        'two_images_section' => 'success',
                        'two_images_reverse' => 'warning',
                        'video_text_section' => 'danger',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New section'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function storeAsWebp(
        TemporaryUploadedFile $file,
        string $directory,
        int $targetWidth,
        int $targetHeight,
    ): string {
        if (! function_exists('imagecreatetruecolor')) {
            return $file->store($directory, 'public');
        }

        $sourcePath = $file->getRealPath();
        $mimeType = $file->getMimeType();

        $sourceImage = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, 'public');
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $targetRatio = $targetWidth / $targetHeight;
        $sourceRatio = $sourceWidth / $sourceHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) round(($sourceHeight - $cropHeight) / 2);
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        Storage::disk('public')->makeDirectory($directory);

        $path = trim($directory, '/') . '/' . Str::uuid() . '.webp';
        $fullPath = Storage::disk('public')->path($path);

        imagewebp($targetImage, $fullPath, 85);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $path;
    }
}
