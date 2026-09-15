<?php

namespace App\Filament\Pages\Spa;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class InformationBarSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Information Bar';

    protected static ?string $title = 'SPA Information Bar Settings';

    protected static ?string $slug = 'spa/information-bar';

    protected static ?int $navigationSort = 20;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected function pageDescription(): string
    {
        return 'Manage the four ordered information items displayed directly below the SPA hero.';
    }

    protected function ownedFields(): array
    {
        return ['information_bar_items'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('SPA Information Bar')->columnSpanFull()->schema([
                Repeater::make('information_bar_items')
                    ->columns(2)
                    ->minItems(4)
                    ->maxItems(4)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => $state['label'] ?? 'Information item')
                    ->addable(false)
                    ->deletable(false)
                    ->schema([
                        Select::make('icon')->options([
                            'clock' => 'Clock',
                            'calendar' => 'Calendar',
                            'location' => 'Location',
                            'phone' => 'Phone',
                        ])->native(false)->required(),
                        TextInput::make('label')->required()->maxLength(255),
                        Textarea::make('value')->required()->rows(2)->maxLength(1000)
                            ->helperText('Line breaks are preserved on the website.'),
                        TextInput::make('link')->label('Optional Link / Action')->maxLength(2048)
                            ->rule('nullable')
                            ->rule('regex:/^(https?:\/\/|mailto:|tel:|\/|#).+/i')
                            ->helperText('Use an http(s), mailto:, tel:, root-relative, or # anchor URL.'),
                    ]),
            ]),
        ];
    }
}
