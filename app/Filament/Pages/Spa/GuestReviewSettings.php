<?php

namespace App\Filament\Pages\Spa;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GuestReviewSettings extends SpaSettingsPage
{
    protected static ?string $navigationLabel = 'Guest Review';

    protected static ?string $title = 'SPA Guest Review Settings';

    protected static ?string $slug = 'spa/guest-review';

    protected static ?int $navigationSort = 70;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected function pageDescription(): string
    {
        return 'Manage the featured guest quote and supporting SPA image.';
    }

    protected function ownedFields(): array
    {
        return [
            'guest_review_visible',
            'guest_review_quote',
            'guest_review_label',
            'guest_review_image',
            'guest_review_image_alt',
        ];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Guest Quote')->columnSpan(7)->schema([
                Toggle::make('guest_review_visible')->label('Show Section')->default(true),
                Textarea::make('guest_review_quote')->label('Quote')->rows(7),
                TextInput::make('guest_review_label')->label('Quote Label')->maxLength(255),
            ]),
            Section::make('Media')->columnSpan(5)->schema([
                self::withImagePreview(FileUpload::make('guest_review_image')
                    ->label('Review Image')
                    ->disk('public')
                    ->directory('spa/guest-review')
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imagePreviewHeight('220')
                    ->panelAspectRatio('16:9')
                    ->panelLayout('integrated')
                    ->openable()
                    ->downloadable()
                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                        file: $file,
                        directory: 'spa/guest-review',
                        targetWidth: 1600,
                        targetHeight: 900,
                    ))),
                TextInput::make('guest_review_image_alt')->label('Image Alt Text')->maxLength(255),
            ]),
        ];
    }
}
