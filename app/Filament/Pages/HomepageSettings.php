<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Models\HomepageSetting;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class HomepageSettings extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Homepage';

    protected static ?string $title = 'Homepage Settings';

    protected static ?string $slug = 'settings/homepage';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Role::ADMINISTRATOR) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill(HomepageSetting::query()->findOrFail(1)->attributesToArray());
    }

    public function getSubheading(): ?string
    {
        return 'Edit homepage section headings, introductions, and button labels.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('introduction')
                ->label('Edit Homepage Introduction')
                ->url(PageResource::getUrl('edit', ['record' => 1])),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $sections = [];

        foreach (HomepageSetting::SECTIONS as $key => $label) {
            $fields = [
                TextInput::make($key.'_heading')->label('Heading')->required()->maxLength(255)->columnSpanFull(),
                Textarea::make($key.'_description')->label('Introduction')->required()->maxLength(5000)->rows(5)->columnSpanFull(),
                TextInput::make($key.'_action_label')->label('Card Button Label')->required()->maxLength(100),
            ];

            if (in_array($key, ['experiences', 'offers', 'adventures'], true)) {
                $fields[] = TextInput::make($key.'_footer_label')->label('Section Button Label')->required()->maxLength(100);
            }

            $sections[] = Section::make($label)->columns(2)->schema($fields);
        }

        $sections[] = Section::make('Featured Royal Suite')->schema([
            TextInput::make('featured_suite_action_label')->label('Button Label')->required()->maxLength(100),
        ]);
        $sections[] = Section::make('Guest Reviews')->schema([
            TextInput::make('guest_reviews_heading')->label('Heading')->required()->maxLength(255),
            TextInput::make('guest_reviews_action_label')->label('Button Label')->required()->maxLength(100),
        ]);

        return $schema->model(HomepageSetting::class)->statePath('data')->columns(2)->components($sections);
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);

        $settings = HomepageSetting::query()->findOrFail(1);
        $settings->update($this->form->getState());
        $this->form->fill($settings->attributesToArray());

        Notification::make()->title('Homepage settings saved')->success()->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save Changes')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }
}
