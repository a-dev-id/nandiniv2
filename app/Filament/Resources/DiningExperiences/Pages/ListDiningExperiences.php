<?php
namespace App\Filament\Resources\DiningExperiences\Pages;
use App\Filament\Resources\DiningExperiences\DiningExperienceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListDiningExperiences extends ListRecords { protected static string $resource = DiningExperienceResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
