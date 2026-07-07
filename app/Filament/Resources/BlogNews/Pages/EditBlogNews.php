<?php

namespace App\Filament\Resources\BlogNews\Pages;

use App\Filament\Resources\BlogNews\BlogNewsResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\URL;

class EditBlogNews extends EditRecord
{
    protected static string $resource = BlogNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon(Heroicon::OutlinedEye)
                ->url(fn(): string => URL::temporarySignedRoute(
                    'blog.preview',
                    now()->addMinutes(30),
                    ['blogNews' => $this->record],
                ))
                ->openUrlInNewTab()
                ->visible(fn(): bool => filled($this->record->slug)),

            DeleteAction::make(),
        ];
    }
}
