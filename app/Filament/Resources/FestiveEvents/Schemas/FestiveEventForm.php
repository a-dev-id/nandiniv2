<?php

namespace App\Filament\Resources\FestiveEvents\Schemas;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FestiveEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Grid::make()
                    ->columnSpan(['default' => 12, 'lg' => 8])
                    ->schema([
                        Section::make('Page & Hero Content')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                                TextInput::make('slug')->required()->maxLength(191)->unique(ignoreRecord: true),
                                TextInput::make('hero_eyebrow')->label('Date / Eyebrow')->maxLength(255),
                                TextInput::make('hero_subheading')->label('Subheading')->maxLength(255),
                                Textarea::make('hero_heading')->label('Heading')->rows(3)->columnSpanFull()
                                    ->helperText('Line breaks are preserved on the website.'),
                                Textarea::make('hero_description')->label('Description')->rows(4)->columnSpanFull(),
                                TextInput::make('hero_price')->label('Price')->maxLength(255),
                                TextInput::make('hero_button_label')->label('Button Label')->maxLength(255),
                                self::linkInput('hero_button_url', 'Button URL'),
                            ]),
                        Section::make('Dinner Menu')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('menu_visible')->label('Show Dinner Menu')->default(true)->columnSpanFull(),
                                TextInput::make('menu_eyebrow')->label('Eyebrow')->maxLength(255),
                                Textarea::make('menu_heading')->label('Heading')->rows(2),
                                Textarea::make('menu_description')->label('Description')->rows(3)->columnSpanFull(),
                                Repeater::make('menu_items')
                                    ->label('Menu Courses')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->minItems(1)
                                    ->maxItems(20)
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): string => $state['title'] ?? 'Menu course')
                                    ->schema([
                                        Select::make('type')
                                            ->options(['dish' => 'Dish', 'intermezzo' => 'Intermezzo'])
                                            ->default('dish')
                                            ->required(),
                                        TextInput::make('label')->label('Small Label')->maxLength(100)
                                            ->helperText('Useful for an intermezzo.'),
                                        TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                                        Textarea::make('description')->rows(3)->columnSpanFull(),
                                        self::imageUpload('image', 'Course Image', 'festive/events/menu', 1200, 900, '4:3'),
                                        TextInput::make('image_alt')->label('Image Alt Text')->maxLength(255),
                                    ]),
                            ]),
                        Section::make('Evening Programme')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('programme_visible')->label('Show Programme')->default(false)->columnSpanFull(),
                                TextInput::make('programme_eyebrow')->label('Eyebrow')->maxLength(255),
                                Textarea::make('programme_heading')->label('Heading')->rows(2),
                                Repeater::make('programme_items')
                                    ->label('Programme Schedule')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): string => trim(($state['time'] ?? '').' — '.($state['activity'] ?? ''), ' —'))
                                    ->schema([
                                        TextInput::make('time')->required()->maxLength(255),
                                        TextInput::make('activity')->required()->maxLength(500),
                                    ]),
                            ]),
                        Section::make('Final Reservation Call to Action')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('reservation_visible')->label('Show Reservation CTA')->default(true)->columnSpanFull(),
                                TextInput::make('reservation_eyebrow')->label('Eyebrow')->maxLength(255),
                                Textarea::make('reservation_heading')->label('Heading')->rows(2),
                                Textarea::make('reservation_description')->label('Description')->rows(3)->columnSpanFull(),
                                TextInput::make('reservation_button_label')->label('Button Label')->maxLength(255),
                                self::linkInput('reservation_button_url', 'Button URL'),
                            ]),
                    ]),
                Grid::make()
                    ->columnSpan(['default' => 12, 'lg' => 4])
                    ->schema([
                        Section::make('Publishing')
                            ->schema([
                                Toggle::make('is_active')->label('Active')->default(true)->required(),
                                TextInput::make('sort_order')->label('Display Order')->numeric()->minValue(0)->default(1)->required(),
                            ]),
                        Section::make('Hero Image')
                            ->schema([
                                self::imageUpload('hero_image', 'Hero Image', 'festive/events/hero', 1920, 1080, '16:9'),
                                TextInput::make('hero_image_alt')->label('Image Alt Text')->maxLength(255),
                            ]),
                        Section::make('Programme Image')
                            ->schema([
                                self::imageUpload('programme_image', 'Programme Image', 'festive/events/programme', 1400, 1050, '4:3'),
                                TextInput::make('programme_image_alt')->label('Image Alt Text')->maxLength(255),
                            ]),
                        Section::make('SEO')
                            ->schema([
                                TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                                Textarea::make('meta_description')->label('Meta Description')->maxLength(180)->rows(4),
                            ]),
                    ]),
            ]);
    }

    private static function linkInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->maxLength(2048)
            ->rule('nullable')
            ->rule('regex:/^(https?:\/\/|mailto:|tel:|\/|#).+/i')
            ->helperText('Use an http(s), mailto:, tel:, root-relative, or # anchor URL.');
    }

    private static function imageUpload(
        string $name,
        string $label,
        string $directory,
        int $width,
        int $height,
        string $ratio,
    ): FileUpload {
        return FileUpload::make($name)
            ->label($label)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imagePreviewHeight('180')
            ->panelAspectRatio($ratio)
            ->panelLayout('integrated')
            ->openable()
            ->downloadable()
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
            })
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                file: $file,
                directory: $directory,
                targetWidth: $width,
                targetHeight: $height,
            ));
    }
}
