<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class OurPhilosophySettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Our Philosophy';

    protected static ?string $title = 'Our Philosophy Settings';

    protected static ?string $slug = 'dining/our-philosophy';

    protected static ?int $navigationSort = 20;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected function pageDescription(): string
    {
        return 'Manage the content and image used in the Our Philosophy section.';
    }

    protected function ownedFields(): array
    {
        return ['philosophy_eyebrow', 'philosophy_heading', 'philosophy_description', 'philosophy_image', 'philosophy_image_alt', 'philosophy_accent_text'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpan(7)->schema([
                TextInput::make('philosophy_eyebrow')->label('Eyebrow / Label')->maxLength(255),
                Textarea::make('philosophy_heading')->label('Main Heading')->rows(3),
                Textarea::make('philosophy_description')->label('Description')->rows(6)->columnSpanFull(),
                Textarea::make('philosophy_accent_text')->label('Handwritten / Accent Text')->rows(4)->columnSpanFull(),
            ]),
            Section::make('Media')->columnSpan(5)->schema([
                DiningSettingResource::imageUpload('philosophy_image', 'dining/philosophy', 1200, 900),
                TextInput::make('philosophy_image_alt')->label('Image Alt Text')->maxLength(255),
            ]),
        ];
    }
}
