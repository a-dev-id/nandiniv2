<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class WhyDineSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Why Dine';

    protected static ?string $title = 'Why Dine Settings';

    protected static ?string $slug = 'dining/why-dine';

    protected static ?int $navigationSort = 30;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected function pageDescription(): string
    {
        return 'Manage the heading and ordered benefits shown in Why Dine at Nandini.';
    }

    protected function ownedFields(): array
    {
        return ['why_dine_eyebrow', 'why_dine_heading', 'why_dine_items'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpanFull()->schema([
                TextInput::make('why_dine_eyebrow')->label('Eyebrow')->maxLength(255),
                Textarea::make('why_dine_heading')->label('Main Heading')->rows(3),
            ]),
            Section::make('Benefit Items')->columnSpanFull()->schema([
                Repeater::make('why_dine_items')->columns(3)->minItems(1)->maxItems(8)->reorderable()->collapsible()->cloneable()
                    ->itemLabel(fn (array $state): string => str_replace(["\r", "\n"], ' ', $state['title'] ?? 'Benefit item'))
                    ->addActionLabel('Add benefit item')->schema([
                        Select::make('icon')->options(DiningSettingResource::iconOptions())->native(false)->searchable()->preload()->required(),
                        Textarea::make('title')->rows(2)->required(), Textarea::make('description')->rows(3)->required(),
                    ]),
            ]),
        ];
    }
}
