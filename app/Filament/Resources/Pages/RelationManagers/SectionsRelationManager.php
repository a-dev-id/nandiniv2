<?php

namespace App\Filament\Resources\Pages\RelationManagers;

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

    protected static ?string $title = 'Page Sections';

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
                            ->options([
                                'intro' => 'Intro / Text Section',
                                'split_media' => 'Split Media Section',
                                'split_media_reverse' => 'Split Media Reverse',
                                'two_column_text' => 'Two Column Text',
                                'image_banner' => 'Image Banner',
                                'three_images' => 'Three Images Section',
                                'gallery' => 'Gallery Section',
                                'cta_banner' => 'CTA Banner',
                                'custom' => 'Custom Section',
                            ])
                            ->default('intro')
                            ->required(),

                        TextInput::make('title')
                            ->maxLength(255),

                        TextInput::make('subtitle')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
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
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Desktop Image')
                                    ->disk('public')
                                    ->directory('pages/sections')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('16:9')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'pages/sections',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('image_alt')
                                    ->label('Desktop Alt Text')
                                    ->placeholder('Describe the desktop image for SEO and accessibility')
                                    ->maxLength(255),

                                FileUpload::make('mobile_image')
                                    ->label('Mobile Image')
                                    ->disk('public')
                                    ->directory('pages/sections/mobile')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('4:3')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'pages/sections/mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('mobile_image_alt')
                                    ->label('Mobile Alt Text')
                                    ->placeholder('Describe the mobile image')
                                    ->maxLength(255),

                                TextInput::make('caption')
                                    ->label('Caption')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('Active')
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
                                    ->maxLength(255),

                                TextInput::make('button_url')
                                    ->label('Button URL')
                                    ->maxLength(255),
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
                    ->getStateUsing(fn($record): ?string => $record->images()->orderBy('sort_order')->first()?->image),

                TextColumn::make('title')
                    ->label('Section')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->placeholder('Untitled section')
                    ->description(fn($record): ?string => $record->subtitle),

                TextColumn::make('section_key')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'intro' => 'Intro',
                        'split_media' => 'Split Media',
                        'split_media_reverse' => 'Split Media Reverse',
                        'two_column_text' => 'Two Column Text',
                        'image_banner' => 'Image Banner',
                        'three_images' => 'Three Images',
                        'gallery' => 'Gallery',
                        'cta_banner' => 'CTA Banner',
                        'custom' => 'Custom',
                        default => $state ? Str::headline($state) : '-',
                    }),

                TextColumn::make('images_count')
                    ->label('Images')
                    ->counts('images')
                    ->sortable(),

                TextColumn::make('excerpt')
                    ->label('Excerpt')
                    ->limit(60)
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New section')
                    ->mutateDataUsing(function (array $data): array {
                        $data['sort_order'] = ($this->getOwnerRecord()->sections()->max('sort_order') ?? 0) + 1;

                        return $data;
                    }),
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

    private static function storeAsWebp(
        TemporaryUploadedFile $file,
        string $directory,
        int $targetWidth,
        int $targetHeight,
    ): string {
        $disk = Storage::disk('public');

        $disk->makeDirectory($directory);

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

        if (in_array($mimeType, ['image/jpeg', 'image/jpg'], true) && function_exists('exif_read_data')) {
            $sourceImage = self::fixImageOrientation($sourceImage, $sourcePath);
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropHeight = $sourceHeight;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
        }

        $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
        $cropY = (int) round(($sourceHeight - $cropHeight) / 2);

        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        imagecopyresampled(
            $finalImage,
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

        $path = $directory . '/' . Str::uuid() . '.webp';
        $fullPath = $disk->path($path);

        imagewebp($finalImage, $fullPath, 82);

        imagedestroy($sourceImage);
        imagedestroy($finalImage);

        return $path;
    }

    private static function fixImageOrientation($image, string $sourcePath)
    {
        $exif = @exif_read_data($sourcePath);

        if (! isset($exif['Orientation'])) {
            return $image;
        }

        return match ((int) $exif['Orientation']) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }
}
