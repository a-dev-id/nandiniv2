<?php
namespace App\Filament\Resources\DiningReviews\Pages;
use App\Filament\Resources\DiningReviews\DiningReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListDiningReviews extends ListRecords { protected static string $resource = DiningReviewResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
