<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Role;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewDashboard')
                ->label('View Dashboard')
                ->icon(Heroicon::OutlinedEye)
                ->url(fn (): string => route('membership.dashboard.preview', ['member' => $this->record]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth('web')->user()?->hasRole(Role::ADMINISTRATOR) ?? false),
        ];
    }
}
