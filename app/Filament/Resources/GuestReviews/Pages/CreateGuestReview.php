<?php

namespace App\Filament\Resources\GuestReviews\Pages;

use App\Filament\Resources\GuestReviews\GuestReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestReview extends CreateRecord
{
    protected static string $resource = GuestReviewResource::class;
}
