<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class DishOfTheMonthSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Dish of the Month';

    protected static ?string $title = 'Dish of the Month Settings';

    protected static ?string $slug = 'dining/dish-of-the-month';

    protected static ?int $navigationSort = 46;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected function pageDescription(): string
    {
        return 'Manage the full-width Dish of the Month banner shown after Signature Dish.';
    }

    protected function ownedFields(): array
    {
        return ['signature_dishes', 'signature_menu_label', 'signature_menu_url'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Dish Content & Image')->columnSpanFull()->schema([
                Repeater::make('signature_dishes')
                    ->label('Dish of the Month')
                    ->minItems(1)
                    ->maxItems(1)
                    ->defaultItems(1)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columns(2)
                    ->schema([
                        TextInput::make('eyebrow')->label('Eyebrow')->maxLength(255),
                        TextInput::make('heading')->label('Heading')->maxLength(255),
                        Textarea::make('introduction')->label('Description')->rows(5)->columnSpanFull(),
                        TextInput::make('label')->label('Supporting Label')->maxLength(255),
                        TextInput::make('title')->label('Panel Title')->maxLength(255),
                        TextInput::make('price_display')->label('Price')->maxLength(255),
                        Textarea::make('panel_description')->label('Panel Description')->rows(4)->columnSpanFull(),
                        DiningSettingResource::imageUpload('image', 'dining/dish-of-the-month', 1920, 1080, '16:9'),
                        TextInput::make('alt')->label('Image Alt Text')->maxLength(255),
                    ])
                    ->columnSpanFull(),
            ]),
            Section::make('Menu Action')->columns(2)->columnSpanFull()->schema([
                TextInput::make('signature_menu_label')->label('CTA Label')->maxLength(255),
                DiningSettingResource::linkInput('signature_menu_url', 'CTA URL'),
            ]),
        ];
    }
}
