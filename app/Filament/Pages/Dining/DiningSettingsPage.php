<?php

namespace App\Filament\Pages\Dining;

use App\Models\DiningSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Locked;
use UnitEnum;

abstract class DiningSettingsPage extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Dining Landing Page';

    protected static string|BackedEnum|null $navigationIcon = null;

    protected Width|string|null $maxContentWidth = Width::Full;

    public ?array $data = [];

    #[Locked]
    public ?int $settingsId = null;

    abstract protected function formComponents(): array;

    abstract protected function ownedFields(): array;

    abstract protected function pageDescription(): string;

    public function mount(): void
    {
        $settings = DiningSetting::query()->firstOrCreate();
        $this->settingsId = $settings->getKey();
        $this->form->fill($settings->only($this->ownedFields()));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(DiningSetting::class)
            ->statePath('data')
            ->columns(12)
            ->components($this->formComponents());
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $ownedData = array_intersect_key($data, array_flip($this->ownedFields()));

        DiningSetting::query()->findOrFail($this->settingsId)->update($ownedData);

        Notification::make()
            ->title('Dining settings saved')
            ->success()
            ->send();

        $this->form->fill($ownedData);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->pageDescription();
    }

    public function getBreadcrumbs(): array
    {
        return ['Dining Landing Page', static::getNavigationLabel()];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Save Changes')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }
}
