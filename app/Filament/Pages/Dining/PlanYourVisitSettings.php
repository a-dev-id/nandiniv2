<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class PlanYourVisitSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Plan Your Visit';

    protected static ?string $title = 'Plan Your Visit Settings';

    protected static ?string $slug = 'dining/plan-your-visit';

    protected static ?int $navigationSort = 60;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected function pageDescription(): string
    {
        return 'Manage practical information and menu buttons displayed beside the FAQ.';
    }

    protected function ownedFields(): array
    {
        return [
            'visit_eyebrow',
            'visit_heading',
            'visit_information_items',
            'visit_food_menu_label',
            'visit_food_menu_url',
            'visit_premium_menu_label',
            'visit_premium_menu_url',
            'visit_beverage_menu_label',
            'visit_beverage_menu_url',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpanFull()->schema([
                TextInput::make('visit_eyebrow')->label('Eyebrow')->maxLength(255), Textarea::make('visit_heading')->label('Heading')->rows(2),
            ]),
            Section::make('Information Items')->columnSpanFull()->schema([
                Repeater::make('visit_information_items')->columns(2)->minItems(1)->maxItems(12)->reorderable()->collapsible()
                    ->itemLabel(fn (array $state): string => $state['label'] ?? 'Visit information')
                    ->addActionLabel('Add information item')->schema(DiningSettingResource::informationItemFields(true)),
            ]),
            Section::make('Buttons')->columns(2)->columnSpanFull()->schema([
                TextInput::make('visit_food_menu_label')->label('Food Menu Button Label')->maxLength(255),
                DiningSettingResource::linkInput('visit_food_menu_url', 'Food Menu URL'),
                TextInput::make('visit_premium_menu_label')->label('Premium Menu Button Label')->maxLength(255),
                DiningSettingResource::linkInput('visit_premium_menu_url', 'Premium Menu URL'),
                TextInput::make('visit_beverage_menu_label')->label('Beverage Button Label')->maxLength(255),
                DiningSettingResource::linkInput('visit_beverage_menu_url', 'Beverage Menu URL'),
            ]),
        ];
    }
}
