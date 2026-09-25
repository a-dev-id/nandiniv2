<?php

namespace App\Filament\Pages\Spa;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class WhyNandiniSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Why Nandini';

    protected static ?string $title = 'Why Nandini Settings';

    protected static ?string $slug = 'spa/why-nandini';

    protected static ?int $navigationSort = 40;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected function pageDescription(): string
    {
        return 'Manage the heading and four wellness benefits shown in the Why Nandini section.';
    }

    protected function ownedFields(): array
    {
        return ['why_nandini_visible', 'why_nandini_eyebrow', 'why_nandini_heading', 'why_nandini_items'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpanFull()->schema([
                Toggle::make('why_nandini_visible')->label('Show Section')->default(true)->columnSpanFull(),
                TextInput::make('why_nandini_eyebrow')->label('Eyebrow')->maxLength(255),
                Textarea::make('why_nandini_heading')->label('Main Heading')->rows(3),
            ]),
            Section::make('Wellness Benefits')->columnSpanFull()->schema([
                Repeater::make('why_nandini_items')
                    ->columns(3)
                    ->minItems(4)
                    ->maxItems(4)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => str_replace(["\r", "\n"], ' ', $state['title'] ?? 'Wellness benefit'))
                    ->addable(false)
                    ->deletable(false)
                    ->schema([
                        Select::make('icon')->options([
                            'jungle' => 'Jungle / Tropical Leaves',
                            'ritual' => 'Meditation / Ritual',
                            'care' => 'Heart / Care',
                            'river' => 'River / Nature',
                        ])->native(false)->required(),
                        Textarea::make('title')->rows(2)->required(),
                        Textarea::make('description')->rows(3)->required(),
                    ]),
            ]),
        ];
    }
}
