<?php

namespace App\Filament\Pages\Spa;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GeneralSpaSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'General';

    protected static ?string $title = 'SPA General Settings';

    protected static ?string $slug = 'spa/general';

    protected static ?int $navigationSort = 10;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected function pageDescription(): string
    {
        return 'Manage the hero, reservation contact and SEO defaults for the SPA Landing Page.';
    }

    protected function ownedFields(): array
    {
        return [
            'reservation_whatsapp', 'reservation_url',
            'meta_title', 'meta_description', 'meta_author', 'meta_site_name',
            'hero_image', 'hero_mobile_image', 'hero_image_alt', 'hero_mobile_image_alt',
            'hero_eyebrow', 'hero_heading', 'hero_description',
            'hero_primary_cta_label', 'hero_primary_cta_url',
            'hero_secondary_cta_label', 'hero_secondary_cta_url',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Hero Content')->columns(2)->columnSpan(7)->schema([
                TextInput::make('hero_eyebrow')->label('Eyebrow')->maxLength(255)->columnSpanFull(),
                Textarea::make('hero_heading')->label('Heading')->rows(3)->columnSpanFull()
                    ->helperText('Line breaks are preserved on the website.'),
                Textarea::make('hero_description')->label('Description')->rows(5)->columnSpanFull(),
                TextInput::make('hero_primary_cta_label')->label('Primary CTA Label')->maxLength(255),
                self::linkInput('hero_primary_cta_url', 'Primary CTA URL'),
                TextInput::make('hero_secondary_cta_label')->label('Secondary CTA Label')->maxLength(255),
                self::linkInput('hero_secondary_cta_url', 'Secondary CTA URL'),
            ]),
            Section::make('Hero Media')->columnSpan(5)->schema([
                self::imageUpload('hero_image', 'Desktop Hero Image', 'spa/hero', 1920, 1080, '16:9'),
                TextInput::make('hero_image_alt')->label('Desktop Image Alt Text')->maxLength(255),
                self::imageUpload('hero_mobile_image', 'Mobile Hero Image', 'spa/hero-mobile', 900, 1200, '3:4'),
                TextInput::make('hero_mobile_image_alt')->label('Mobile Image Alt Text')->maxLength(255),
            ]),
            Section::make('Reservations')->columns(2)->columnSpan(7)->schema([
                TextInput::make('reservation_whatsapp')->label('WhatsApp Number')->tel()->maxLength(50),
                self::linkInput('reservation_url', 'WhatsApp URL'),
            ]),
            Section::make('SEO Defaults')->columnSpan(5)->schema([
                TextInput::make('meta_title')->maxLength(70),
                Textarea::make('meta_description')->maxLength(180)->rows(4),
                TextInput::make('meta_author')->maxLength(255),
                TextInput::make('meta_site_name')->label('Open Graph Site Name')->maxLength(255),
            ]),
        ];
    }

    private static function linkInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)->maxLength(2048)
            ->rule('nullable')
            ->rule('regex:/^(https?:\/\/|mailto:|tel:|\/|#).+/i')
            ->helperText('Use an http(s), mailto:, tel:, root-relative, or # anchor URL.');
    }

    private static function imageUpload(string $name, string $label, string $directory, int $width, int $height, string $ratio): FileUpload
    {
        return FileUpload::make($name)->label($label)->disk('public')->directory($directory)
            ->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imagePreviewHeight('220')->panelAspectRatio($ratio)->panelLayout('integrated')->openable()->downloadable()
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                file: $file,
                directory: $directory,
                targetWidth: $width,
                targetHeight: $height,
            ));
    }
}
