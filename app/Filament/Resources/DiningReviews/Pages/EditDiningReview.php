<?php
namespace App\Filament\Resources\DiningReviews\Pages;
use App\Filament\Resources\DiningReviews\DiningReviewResource;
use Filament\Resources\Pages\EditRecord;

class EditDiningReview extends EditRecord
{
    protected static string $resource = DiningReviewResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['show_on_dining'] = true;

        return $data;
    }
}
