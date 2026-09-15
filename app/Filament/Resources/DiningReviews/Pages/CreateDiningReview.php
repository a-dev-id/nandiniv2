<?php
namespace App\Filament\Resources\DiningReviews\Pages;
use App\Filament\Resources\DiningReviews\DiningReviewResource;
use Filament\Resources\Pages\CreateRecord;
class CreateDiningReview extends CreateRecord { protected static string $resource = DiningReviewResource::class; protected function mutateFormDataBeforeCreate(array $data): array { $data['show_on_dining'] = true; return $data; } }
