<?php

namespace App\Filament\Pages\Festive;

use App\Models\FestiveSetting;
use App\Support\FilamentWebpUpload;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class FestiveSettings extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Festive Landing Page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Festive Content';

    protected static ?string $title = 'Festive Landing Page';

    protected static ?string $slug = 'festive';

    protected static ?int $navigationSort = 10;

    protected Width|string|null $maxContentWidth = Width::Full;

    public ?array $data = [];

    #[Locked]
    public ?int $settingsId = null;

    public function mount(): void
    {
        $settings = FestiveSetting::query()->firstOrCreate();
        $this->settingsId = $settings->getKey();
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(FestiveSetting::class)
            ->statePath('data')
            ->columns(12)
            ->components([
                Section::make('Hero Content')
                    ->description('The full-height opening panel below the shared website navigation.')
                    ->columns(2)
                    ->columnSpan(7)
                    ->schema([
                        Toggle::make('hero_visible')->label('Show Hero')->default(true)->columnSpanFull(),
                        TextInput::make('hero_eyebrow')->label('Eyebrow')->maxLength(255),
                        TextInput::make('hero_subheading')->label('Subheading')->maxLength(255),
                        Textarea::make('hero_heading')->label('Heading')->rows(3)->columnSpanFull()
                            ->helperText('Line breaks are preserved on the website.'),
                        Textarea::make('hero_description')->label('Description')->rows(4)->columnSpanFull(),
                        TextInput::make('hero_primary_cta_label')->label('Primary Button Label')->maxLength(255),
                        self::linkInput('hero_primary_cta_url', 'Primary Button URL'),
                        TextInput::make('hero_secondary_cta_label')->label('Secondary Button Label')->maxLength(255),
                        self::linkInput('hero_secondary_cta_url', 'Secondary Button URL'),
                    ]),
                Section::make('Hero Media')
                    ->columnSpan(5)
                    ->schema([
                        self::imageUpload('hero_image', 'Hero Image', 'festive/hero', 1920, 1080, '16:9'),
                        TextInput::make('hero_image_alt')->label('Image Alt Text')->maxLength(255),
                    ]),
                Section::make('Introduction')
                    ->description('The centered introduction directly below the hero.')
                    ->columns(2)
                    ->columnSpan(7)
                    ->schema([
                        Toggle::make('introduction_visible')->label('Show Introduction')->default(true)->columnSpanFull(),
                        TextInput::make('introduction_eyebrow')->label('Eyebrow')->maxLength(255),
                        Textarea::make('introduction_heading')->label('Heading')->rows(2),
                        Textarea::make('introduction_description')->label('Description')->rows(5)->columnSpanFull(),
                    ]),
                Section::make('SEO Defaults')
                    ->columnSpan(5)
                    ->schema([
                        TextInput::make('meta_title')->maxLength(70),
                        Textarea::make('meta_description')->maxLength(180)->rows(4),
                        TextInput::make('meta_author')->maxLength(255),
                        TextInput::make('meta_site_name')->label('Open Graph Site Name')->maxLength(255),
                    ]),
                Section::make('Celebrations')
                    ->description('Add, reorder or remove the alternating festive experience panels.')
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('celebrations_visible')->label('Show Celebrations')->default(true),
                        Repeater::make('celebrations')
                            ->label('Celebration Panels')
                            ->minItems(1)
                            ->maxItems(8)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->columns(2)
                            ->itemLabel(fn (array $state): string => $state['heading'] ?? 'Celebration')
                            ->addActionLabel('Add celebration')
                            ->schema([
                                TextInput::make('anchor')->label('Section Anchor')->maxLength(100)
                                    ->helperText('Example: christmas. Use this in a button URL as #christmas.'),
                                TextInput::make('date')->maxLength(255),
                                Textarea::make('heading')->rows(2)->required()->columnSpanFull(),
                                Textarea::make('description')->rows(4)->columnSpanFull(),
                                TextInput::make('price')->maxLength(255),
                                TextInput::make('button_label')->label('Button Label')->maxLength(255),
                                self::linkInput('button_url', 'Button URL'),
                                self::imageUpload('image', 'Image', 'festive/celebrations', 1440, 1080, '4:3'),
                                TextInput::make('image_alt')->label('Image Alt Text')->maxLength(255)->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Festive Programme')
                    ->description('Programme days and their ordered schedule items.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('programme_visible')->label('Show Programme')->default(true)->columnSpanFull(),
                        TextInput::make('programme_eyebrow')->label('Eyebrow')->maxLength(255),
                        Textarea::make('programme_heading')->label('Heading')->rows(2),
                        Repeater::make('programme_days')
                            ->label('Programme Days')
                            ->minItems(1)
                            ->maxItems(12)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): string => $state['date'] ?? 'Programme day')
                            ->addActionLabel('Add programme day')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('date')->required()->maxLength(255),
                                Repeater::make('items')
                                    ->label('Schedule')
                                    ->minItems(1)
                                    ->maxItems(30)
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): string => trim(($state['time'] ?? '').' — '.($state['activity'] ?? ''), ' —'))
                                    ->addActionLabel('Add schedule item')
                                    ->schema([
                                        TextInput::make('time')->required()->maxLength(255),
                                        TextInput::make('activity')->required()->maxLength(500),
                                    ]),
                            ]),
                    ]),
                Section::make('Final Reservation Call to Action')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Content')->schema([
                            Toggle::make('booking_cta_visible')->label('Show Reservation CTA')->default(true),
                            TextInput::make('booking_cta_eyebrow')->label('Eyebrow')->maxLength(255),
                            Textarea::make('booking_cta_heading')->label('Heading')->rows(3)
                                ->helperText('Line breaks are preserved on the website.'),
                            Textarea::make('booking_cta_description')->label('Description')->rows(4),
                            TextInput::make('booking_cta_button_label')->label('Button Label')->maxLength(255),
                            self::linkInput('booking_cta_button_url', 'Button URL'),
                        ]),
                        Section::make('Background Image')->schema([
                            self::imageUpload('booking_cta_image', 'Image', 'festive/booking-cta', 1920, 900, '16:9'),
                            TextInput::make('booking_cta_image_alt')->label('Image Alt Text')->maxLength(255),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $settings = FestiveSetting::query()->findOrFail($this->settingsId);
        $settings->update($this->form->getState());

        Notification::make()
            ->title('Festive landing page saved')
            ->success()
            ->send();

        $this->form->fill($settings->fresh()->toArray());
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage all copy, images, buttons and schedules shown on the Festive Landing Page.';
    }

    public function getBreadcrumbs(): array
    {
        return ['Festive Landing Page', 'Festive Content'];
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

    private static function linkInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->maxLength(2048)
            ->rule('nullable')
            ->rule('regex:/^(https?:\/\/|mailto:|tel:|\/|#).+/i')
            ->helperText('Use an http(s), mailto:, tel:, root-relative, or # anchor URL.');
    }

    private static function imageUpload(
        string $name,
        string $label,
        string $directory,
        int $width,
        int $height,
        string $ratio,
    ): FileUpload {
        return self::withImagePreview(
            FileUpload::make($name)
                ->label($label)
                ->disk('public')
                ->directory($directory)
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->imagePreviewHeight('220')
                ->panelAspectRatio($ratio)
                ->panelLayout('integrated')
                ->openable()
                ->downloadable()
                ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                    file: $file,
                    directory: $directory,
                    targetWidth: $width,
                    targetHeight: $height,
                ))
        );
    }

    private static function withImagePreview(FileUpload $upload): FileUpload
    {
        return $upload
            ->fetchFileInformation(false)
            ->getUploadedFileUsing(function (FileUpload $component, string $file, string|array|null $storedFileNames): array {
                $url = match (true) {
                    str_starts_with($file, 'http://'), str_starts_with($file, 'https://') => $file,
                    str_starts_with($file, '/') => asset($file),
                    default => asset('storage/'.$file),
                };

                return [
                    'name' => ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                    'size' => 0,
                    'type' => null,
                    'url' => $url,
                ];
            });
    }
}
