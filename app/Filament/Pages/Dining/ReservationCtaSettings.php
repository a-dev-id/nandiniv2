<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class ReservationCtaSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Reservation CTA';

    protected static ?string $title = 'Reservation CTA Settings';

    protected static ?string $slug = 'dining/reservation-cta';

    protected static ?int $navigationSort = 80;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCursorArrowRays;

    protected function pageDescription(): string
    {
        return 'Manage the final background-image reservation panel above the footer.';
    }

    protected function ownedFields(): array
    {
        return ['reservation_cta_background_image', 'reservation_cta_background_image_alt', 'reservation_cta_heading', 'reservation_cta_description', 'reservation_cta_label', 'reservation_cta_url'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Content')->columnSpan(7)->schema([
                Textarea::make('reservation_cta_heading')->label('Heading')->rows(2),
                Textarea::make('reservation_cta_description')->label('Description')->rows(5)->helperText('Line breaks are preserved on the website.'),
                TextInput::make('reservation_cta_label')->label('CTA Label')->maxLength(255),
                DiningSettingResource::linkInput('reservation_cta_url', 'CTA URL'),
            ]),
            Section::make('Background Image')->columnSpan(5)->schema([
                DiningSettingResource::imageUpload('reservation_cta_background_image', 'dining/reservation-cta', 1920, 900, '16:9'),
                TextInput::make('reservation_cta_background_image_alt')->label('Accessibility Description')->maxLength(255),
            ]),
        ];
    }
}
