<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class InformationBarSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'Information Bar';

    protected static ?string $title = 'Information Bar Settings';

    protected static ?string $slug = 'dining/information-bar';

    protected static ?int $navigationSort = 40;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected function pageDescription(): string
    {
        return 'Manage the ordered information items displayed directly below the Dining hero.';
    }

    protected function ownedFields(): array
    {
        return ['information_bar_items'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Restaurant Information Bar')->columnSpanFull()->schema([
                Repeater::make('information_bar_items')->columns(2)->minItems(1)->maxItems(8)->reorderable()->collapsible()
                    ->itemLabel(fn (array $state): string => $state['label'] ?? 'Information item')
                    ->addActionLabel('Add information item')->schema(DiningSettingResource::informationItemFields()),
            ]),
        ];
    }
}
