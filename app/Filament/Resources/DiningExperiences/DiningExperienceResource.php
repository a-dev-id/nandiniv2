<?php

namespace App\Filament\Resources\DiningExperiences;

use App\Filament\Resources\DiningExperiences\Pages\CreateDiningExperience;
use App\Filament\Resources\DiningExperiences\Pages\EditDiningExperience;
use App\Filament\Resources\DiningExperiences\Pages\ListDiningExperiences;
use App\Models\DiningExperience;
use App\Support\FilamentWebpUpload;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class DiningExperienceResource extends Resource
{
    protected static ?string $model = DiningExperience::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Dining Landing Page';

    protected static ?string $navigationLabel = 'Dining Experiences';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        $upload = function (string $field, string $label, string $directory, int $width, int $height): FileUpload {
            return FileUpload::make($field)->label($label)->disk('public')->directory($directory)
                ->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->imagePreviewHeight('180')->panelAspectRatio($width.':'.$height)->panelLayout('integrated')
                ->openable()->downloadable()
                ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                    file: $file, directory: $directory, targetWidth: $width, targetHeight: $height,
                ));
        };

        return $schema->columns(12)->components([
            Section::make('Basic Information')->columns(2)->columnSpanFull()->schema([
                TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->label('Dining URL Slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('card_title')->label('Card Title')->required()->maxLength(255),
                Textarea::make('short_description')->label('Card Short Description')->required()->rows(3)->columnSpanFull(),
                TextInput::make('card_cta_label')->label('Card CTA Label')->required()->maxLength(80),
                Toggle::make('is_active')->label('Published')->default(true),
            ]),
            Section::make('Card Image')->columns(2)->columnSpan(6)->schema([
                $upload('card_image', 'Card Image', 'experiences/cards', 1200, 900)->columnSpanFull(),
                TextInput::make('card_image_alt')->label('Card Image Alt Text')->maxLength(255)->columnSpanFull(),
            ]),
            Section::make('Hero')->columns(2)->columnSpan(6)->schema([
                $upload('hero_image', 'Hero Image', 'experiences/main', 1600, 900),
                $upload('hero_mobile_image', 'Mobile Hero Image', 'experiences/main/mobile', 900, 1200),
                TextInput::make('hero_image_alt')->label('Hero Image Alt Text')->maxLength(255)->columnSpanFull(),
            ]),
            Section::make('Introduction')->columns(2)->columnSpanFull()->schema([
                TextInput::make('intro_eyebrow')->default('The experience')->maxLength(100),
                TextInput::make('page_heading')->maxLength(255),
                RichEditor::make('description')->columnSpanFull(),
            ]),
            Section::make('Practical Information')->columns(2)->columnSpanFull()->schema([
                TextInput::make('opening_hours')->maxLength(255),
                TextInput::make('experience_type')->label('Cuisine / Experience Type')->maxLength(255),
                TextInput::make('location')->maxLength(255),
                TextInput::make('whatsapp_number')->tel()->maxLength(50),
            ]),
            Section::make('CTA / Booking')->columns(2)->columnSpanFull()->schema([
                TextInput::make('menu_cta_label')->maxLength(80),
                TextInput::make('menu_url')->url(),
                TextInput::make('reservation_cta_label')->maxLength(80),
                TextInput::make('reservation_url')->url(),
            ]),
            Section::make('Gallery')->columnSpanFull()->schema([
                Repeater::make('gallery')->relationship('gallery')->orderColumn('sort_order')
                    ->reorderable()->collapsible()->itemLabel(fn (array $state): string => $state['caption'] ?? $state['image_alt'] ?? 'Gallery image')
                    ->schema([
                        $upload('image', 'Image', 'experiences/gallery', 1600, 1200)->required(),
                        TextInput::make('image_alt')->label('Alt Text')->required()->maxLength(255),
                        TextInput::make('caption')->maxLength(255),
                        Toggle::make('is_active')->label('Published')->default(true),
                    ])->columns(2),
            ]),
            Section::make('SEO')->columns(2)->columnSpanFull()->schema([
                TextInput::make('meta_title')->maxLength(70),
                Textarea::make('meta_description')->maxLength(180)->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->columns([
            ImageColumn::make('card_image')->disk('public')->label('Card')->square(),
            TextColumn::make('card_title')->label('Experience')->searchable()->sortable()->weight('semibold'),
            TextColumn::make('slug')->label('URL Slug')->searchable(),
            ToggleColumn::make('is_active')->label('Published'),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiningExperiences::route('/'),
            'create' => CreateDiningExperience::route('/create'),
            'edit' => EditDiningExperience::route('/{record}/edit'),
        ];
    }
}
