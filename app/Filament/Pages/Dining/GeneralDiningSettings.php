<?php

namespace App\Filament\Pages\Dining;

use App\Filament\Resources\DiningSettings\DiningSettingResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class GeneralDiningSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'General';

    protected static ?string $title = 'Dining General Settings';

    protected static ?string $slug = 'dining/general';

    protected static ?int $navigationSort = 10;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected function pageDescription(): string
    {
        return 'Manage reservation, menu, general information and SEO defaults for the Dining Landing Page.';
    }

    protected function ownedFields(): array
    {
        return ['reservation_whatsapp', 'reservation_email', 'reservation_url', 'food_menu_url', 'beverage_menu_url', 'opening_hours', 'cuisine', 'location', 'dress_code', 'outside_guest_information', 'meta_title', 'meta_description', 'meta_author', 'meta_site_name', 'hero_video_id', 'hero_image', 'hero_image_alt', 'hero_eyebrow', 'hero_heading', 'hero_subheading', 'hero_description', 'hero_primary_cta_label', 'hero_primary_cta_url', 'hero_secondary_cta_label', 'hero_secondary_cta_url', 'experiences_eyebrow', 'experiences_heading', 'guest_reviews_heading', 'guest_reviews_see_more_label', 'guest_reviews_see_more_url'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Hero Content')->columns(2)->columnSpan(7)->schema([
                TextInput::make('hero_eyebrow')->label('Eyebrow')->maxLength(255),
                Textarea::make('hero_heading')->label('Heading')->rows(3)->helperText('Line breaks are preserved on the website.'),
                Textarea::make('hero_subheading')->label('Subheading')->rows(3)->columnSpanFull(),
                Textarea::make('hero_description')->label('Description')->rows(4)->columnSpanFull(),
                TextInput::make('hero_primary_cta_label')->label('Primary CTA Label')->maxLength(255),
                DiningSettingResource::linkInput('hero_primary_cta_url', 'Primary CTA URL'),
                TextInput::make('hero_secondary_cta_label')->label('Secondary CTA Label')->maxLength(255),
                DiningSettingResource::linkInput('hero_secondary_cta_url', 'Secondary CTA URL'),
            ]),
            Section::make('Hero Media')->columnSpan(5)->schema([
                TextInput::make('hero_video_id')->label('YouTube Video ID')->maxLength(100),
                DiningSettingResource::imageUpload('hero_image', 'dining/hero', 1920, 1080, '16:9'),
                TextInput::make('hero_image_alt')->label('Image Alt Text')->maxLength(255),
            ]),
            Section::make('Reservations & Menus')->columns(2)->columnSpanFull()->schema([
                TextInput::make('reservation_whatsapp')->tel()->maxLength(50),
                TextInput::make('reservation_email')->email()->maxLength(255),
                TextInput::make('reservation_url')->url()->columnSpanFull(),
                TextInput::make('food_menu_url')->url(), TextInput::make('beverage_menu_url')->url(),
            ]),
            Section::make('Default Practical Information')->columns(2)->columnSpan(7)->schema([
                TextInput::make('opening_hours')->maxLength(255), TextInput::make('cuisine')->maxLength(255),
                TextInput::make('location')->maxLength(255)->columnSpanFull(), TextInput::make('dress_code')->maxLength(255),
                Textarea::make('outside_guest_information')->rows(3)->columnSpanFull(),
            ]),
            Section::make('SEO Defaults')->columnSpan(5)->schema([
                TextInput::make('meta_title')->maxLength(70),
                Textarea::make('meta_description')->maxLength(180)->rows(4),
                TextInput::make('meta_author')->maxLength(255),
                TextInput::make('meta_site_name')->label('Open Graph Site Name')->maxLength(255),
            ]),
            Section::make('Landing Section Labels')->columns(2)->columnSpanFull()->schema([
                TextInput::make('experiences_eyebrow')->label('Dining Experiences Eyebrow')->maxLength(255),
                Textarea::make('experiences_heading')->label('Dining Experiences Heading')->rows(2),
                TextInput::make('guest_reviews_heading')->label('Guest Reviews Heading')->maxLength(255),
                TextInput::make('guest_reviews_see_more_label')->label('Guest Reviews Button Label')->maxLength(255),
                DiningSettingResource::linkInput('guest_reviews_see_more_url', 'Guest Reviews Button URL')->columnSpanFull(),
            ]),
        ];
    }
}
