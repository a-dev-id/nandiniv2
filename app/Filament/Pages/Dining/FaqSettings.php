<?php

namespace App\Filament\Pages\Dining;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class FaqSettings extends DiningSettingsPage
{
    protected static ?string $navigationLabel = 'FAQ';

    protected static ?string $title = 'FAQ Settings';

    protected static ?string $slug = 'dining/faq';

    protected static ?int $navigationSort = 70;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected function pageDescription(): string
    {
        return 'Manage questions and answers displayed on the Dining Landing Page.';
    }

    protected function ownedFields(): array
    {
        return ['faq_eyebrow', 'faq_heading', 'faq_items'];
    }

    protected function formComponents(): array
    {
        return [
            Section::make('Section Content')->columns(2)->columnSpanFull()->schema([
                TextInput::make('faq_eyebrow')->label('Eyebrow')->maxLength(255), Textarea::make('faq_heading')->label('Heading')->rows(2),
            ]),
            Section::make('Questions & Answers')->columnSpanFull()->schema([
                Repeater::make('faq_items')->minItems(1)->maxItems(30)->reorderable()->collapsible()
                    ->itemLabel(fn (array $state): string => $state['question'] ?? 'FAQ')->addActionLabel('Add question')->schema([
                        TextInput::make('question')->required()->maxLength(500), Textarea::make('answer')->required()->rows(4),
                    ]),
            ]),
        ];
    }
}
