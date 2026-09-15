<?php
namespace App\Filament\Resources\DiningExperiences\Pages;
use App\Filament\Resources\DiningExperiences\DiningExperienceResource;
use App\Models\DiningExperience;
use Filament\Resources\Pages\CreateRecord;
class CreateDiningExperience extends CreateRecord { protected static string $resource = DiningExperienceResource::class; protected function mutateFormDataBeforeCreate(array $data): array { $data['sort_order'] ??= (DiningExperience::query()->max('sort_order') ?? 0) + 1; return $data; } }
