<?php

namespace App\Filament\Resources\ExperienceCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ExperienceCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(191)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (! blank($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(191)
                    ->unique(ignoreRecord: true),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
