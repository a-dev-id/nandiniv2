<?php

namespace App\Filament\Resources\Affiliates\Pages;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Filament\Resources\Affiliates\AffiliateResource;
use App\Services\Affiliate\CreateAffiliateService;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAffiliate extends CreateRecord
{
    protected static string $resource = AffiliateResource::class;

    protected static bool $canCreateAnother = false;

    public bool $approveOnCreate = false;

    public function createApproved(): void
    {
        $this->approveOnCreate = true;
        $this->create();
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateAffiliateService::class)->create(
            $data,
            AffiliateRegistrationSource::CreatedByNandini,
            $this->approveOnCreate ? AffiliateStatus::Approved : AffiliateStatus::Pending,
            auth()->user(),
        );
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Save as Pending'),
            Action::make('createApproved')
                ->label('Create and Approve')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('This creates an approved affiliate, activates their short link, and sends a secure password-setup invitation.')
                ->action('createApproved'),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->approveOnCreate ? 'Affiliate created and approved' : 'Pending affiliate created';
    }

    protected function getRedirectUrl(): string
    {
        return AffiliateResource::getUrl('view', ['record' => $this->record]);
    }
}
