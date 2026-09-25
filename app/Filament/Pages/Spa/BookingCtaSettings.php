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

class BookingCtaSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Booking CTA';

    protected static ?string $title = 'SPA Booking CTA Settings';

    protected static ?string $slug = 'spa/booking-cta';

    protected static ?int $navigationSort = 80;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected function pageDescription(): string
    {
        return 'Manage the full-width booking call to action at the bottom of the SPA homepage.';
    }

    protected function ownedFields(): array
    {
        return [
            'booking_cta_visible',
            'booking_cta_eyebrow',
            'booking_cta_heading',
            'booking_cta_description',
            'booking_cta_button_label',
            'booking_cta_button_url',
            'booking_cta_image',
            'booking_cta_image_alt',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Call to Action')->columns(2)->columnSpan(7)->schema([
                Toggle::make('booking_cta_visible')->label('Show Section')->default(true)->columnSpanFull(),
                TextInput::make('booking_cta_eyebrow')->label('Eyebrow')->maxLength(255)->columnSpanFull(),
                Textarea::make('booking_cta_heading')->label('Heading')->rows(2)->columnSpanFull(),
                Textarea::make('booking_cta_description')->label('Description')->rows(5)->columnSpanFull(),
                TextInput::make('booking_cta_button_label')->label('Button Label')->maxLength(100),
                TextInput::make('booking_cta_button_url')->label('Button URL')->maxLength(2048),
            ]),
            Section::make('Background Image')->columnSpan(5)->schema([
                self::withImagePreview(FileUpload::make('booking_cta_image')
                    ->label('Background Image')
                    ->disk('public')
                    ->directory('spa/booking-cta')
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imagePreviewHeight('220')
                    ->panelAspectRatio('16:9')
                    ->panelLayout('integrated')
                    ->openable()
                    ->downloadable()
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                        file: $file,
                        directory: 'spa/booking-cta',
                        targetWidth: 1920,
                        targetHeight: 1080,
                    ))),
                TextInput::make('booking_cta_image_alt')->label('Background Image Alt Text')->maxLength(255),
            ]),
        ];
    }
}
