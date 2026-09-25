<?php

namespace App\Filament\Pages\Spa;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class WellnessJourneysSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Wellness Journeys';

    protected static ?string $title = 'Wellness Journeys Settings';

    protected static ?string $slug = 'spa/wellness-journeys';

    protected static ?int $navigationSort = 50;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSun;

    protected function pageDescription(): string
    {
        return 'Manage the SPA journey carousel, including images, descriptions and booking actions.';
    }

    protected function ownedFields(): array
    {
        return [
            'wellness_journeys_eyebrow',
            'wellness_journeys_visible',
            'wellness_journeys_heading',
            'wellness_journeys_description',
            'wellness_journeys_items',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpanFull()->schema([
                Toggle::make('wellness_journeys_visible')->label('Show Section')->default(true)->columnSpanFull(),
                TextInput::make('wellness_journeys_eyebrow')->label('Eyebrow')->maxLength(255),
                Textarea::make('wellness_journeys_heading')->label('Main Heading')->rows(2),
                Textarea::make('wellness_journeys_description')->label('Description')->rows(4)->columnSpanFull(),
            ]),
            Section::make('Wellness Journey Cards')->columnSpanFull()->schema([
                Repeater::make('wellness_journeys_items')
                    ->label('Journeys')
                    ->minItems(1)
                    ->reorderable()
                    ->collapsible()
                    ->cloneable()
                    ->columns(2)
                    ->itemLabel(fn (array $state): string => str_replace(["\r", "\n"], ' ', $state['title'] ?? 'Wellness journey'))
                    ->schema([
                        Textarea::make('title')->rows(2)->required()->columnSpanFull(),
                        Textarea::make('description')->rows(4)->required()->columnSpanFull(),
                        self::withImagePreview(FileUpload::make('image')
                            ->label('Card Image')
                            ->disk('public')
                            ->directory('spa/wellness-journeys')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('180')
                            ->panelAspectRatio('4:3')
                            ->panelLayout('integrated')
                            ->openable()
                            ->downloadable()
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                                file: $file,
                                directory: 'spa/wellness-journeys',
                                targetWidth: 1200,
                                targetHeight: 900,
                            ))),
                        TextInput::make('image_alt')->label('Image Alt Text')->maxLength(255),
                        TextInput::make('details_label')->label('Details Button Label')->maxLength(100)->default('MORE DETAILS'),
                        TextInput::make('details_url')->label('Details URL')->maxLength(2048)
                            ->helperText('Use /spa-wellness/package-slug for a package on the main website.'),
                        TextInput::make('book_label')->label('Booking Button Label')->maxLength(100)->default('BOOK NOW'),
                        TextInput::make('book_url')->label('Booking URL')->maxLength(2048)
                            ->helperText('Leave blank to use the SPA reservation URL.'),
                    ]),
            ]),
        ];
    }
}
