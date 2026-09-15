<?php

namespace App\Filament\Pages\Spa;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class WellnessPhilosophySettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Wellness Philosophy';

    protected static ?string $title = 'Wellness Philosophy Settings';

    protected static ?string $slug = 'spa/wellness-philosophy';

    protected static ?int $navigationSort = 30;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected function pageDescription(): string
    {
        return 'Manage the editorial wellness philosophy section displayed below the SPA information bar.';
    }

    protected function ownedFields(): array
    {
        return [
            'wellness_philosophy_eyebrow',
            'wellness_philosophy_heading',
            'wellness_philosophy_description',
            'wellness_philosophy_image',
            'wellness_philosophy_image_alt',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpan(7)->schema([
                TextInput::make('wellness_philosophy_eyebrow')->label('Eyebrow / Label')->maxLength(255)->columnSpanFull(),
                Textarea::make('wellness_philosophy_heading')->label('Main Heading')->rows(3)->columnSpanFull()
                    ->helperText('Line breaks are preserved on the website.'),
                Textarea::make('wellness_philosophy_description')->label('Description')->rows(7)->columnSpanFull(),
            ]),
            Section::make('Media')->columnSpan(5)->schema([
                FileUpload::make('wellness_philosophy_image')
                    ->label('Spa Image')
                    ->disk('public')
                    ->directory('spa/wellness-philosophy')
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imagePreviewHeight('220')
                    ->panelAspectRatio('4:3')
                    ->panelLayout('integrated')
                    ->openable()
                    ->downloadable()
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                        file: $file,
                        directory: 'spa/wellness-philosophy',
                        targetWidth: 1200,
                        targetHeight: 900,
                    )),
                TextInput::make('wellness_philosophy_image_alt')->label('Image Alt Text')->maxLength(255),
            ]),
        ];
    }
}
