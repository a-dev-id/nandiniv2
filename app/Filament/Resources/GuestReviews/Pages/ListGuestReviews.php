<?php

namespace App\Filament\Resources\GuestReviews\Pages;

use App\Filament\Resources\GuestReviews\GuestReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGuestReviews extends ListRecords
{
    protected static string $resource = GuestReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
