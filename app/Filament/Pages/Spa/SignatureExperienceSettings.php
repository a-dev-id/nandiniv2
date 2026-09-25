<?php

namespace App\Filament\Pages\Spa;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SignatureExperienceSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Signature Experience';

    protected static ?string $title = 'SPA Signature Experience Settings';

    protected static ?string $slug = 'spa/signature-experience';

    protected static ?int $navigationSort = 60;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected function pageDescription(): string
    {
        return 'Manage the Spa on the River editorial section, including its image and link.';
    }

    protected function ownedFields(): array
    {
        return [
            'signature_visible',
            'signature_eyebrow',
            'signature_heading',
            'signature_description',
            'signature_image',
            'signature_image_alt',
            'signature_link_label',
            'signature_link_url',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpan(7)->schema([
                Toggle::make('signature_visible')->label('Show Section')->default(true)->columnSpanFull(),
                TextInput::make('signature_eyebrow')->label('Eyebrow')->maxLength(255)->columnSpanFull(),
                Textarea::make('signature_heading')->label('Heading')->rows(2)->columnSpanFull()
                    ->helperText('Line breaks are preserved on the website.'),
                Textarea::make('signature_description')->label('Description')->rows(6)->columnSpanFull(),
                TextInput::make('signature_link_label')->label('Link Label')->maxLength(100),
                TextInput::make('signature_link_url')->label('Link URL')->maxLength(2048),
            ]),
            Section::make('Media')->columnSpan(5)->schema([
                self::withImagePreview(FileUpload::make('signature_image')
                    ->label('Section Image')
                    ->disk('public')
                    ->directory('spa/signature-experience')
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
                        directory: 'spa/signature-experience',
                        targetWidth: 1200,
                        targetHeight: 900,
                    ))),
                TextInput::make('signature_image_alt')->label('Image Alt Text')->maxLength(255),
            ]),
        ];
    }
}
