<?php

namespace App\Filament\Resources\GuestReviews\Pages;

use App\Filament\Resources\GuestReviews\GuestReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGuestReview extends EditRecord
{
    protected static string $resource = GuestReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
