<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class PrivateDiningSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Private Dining';

    protected static ?string $title = 'Private Dining Settings';

    protected static ?string $slug = 'dining/private-dining';

    protected static ?int $navigationSort = 50;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected function pageDescription(): string
    {
        return 'Manage the Special Occasions content, image, enquiry action and occasion tags.';
    }

    protected function ownedFields(): array
    {
        return ['private_dining_eyebrow', 'private_dining_heading', 'private_dining_description', 'private_dining_image', 'private_dining_image_alt', 'private_dining_cta_label', 'private_dining_cta_url', 'private_dining_tags'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpan(7)->schema([
                TextInput::make('private_dining_eyebrow')->label('Eyebrow')->maxLength(255),
                Textarea::make('private_dining_heading')->label('Heading')->rows(2),
                Textarea::make('private_dining_description')->label('Description')->rows(5)->columnSpanFull(),
                TextInput::make('private_dining_cta_label')->label('CTA Label')->maxLength(255),
                DiningSettingResource::linkInput('private_dining_cta_url', 'CTA URL'),
            ]),
            Section::make('Media')->columnSpan(5)->schema([
                DiningSettingResource::imageUpload('private_dining_image', 'dining/private-dining', 1200, 900),
                TextInput::make('private_dining_image_alt')->label('Image Alt Text')->maxLength(255),
            ]),
            Section::make('Occasion Tags')->columnSpanFull()->schema([
                Repeater::make('private_dining_tags')->columns(2)->minItems(1)->maxItems(12)->reorderable()->collapsible()
                    ->itemLabel(fn (array $state): string => $state['label'] ?? 'Occasion')->addActionLabel('Add occasion')->schema([
                        TextInput::make('label')->required()->maxLength(255), DiningSettingResource::linkInput('url', 'Optional URL'),
                    ]),
            ]),
        ];
    }
}
