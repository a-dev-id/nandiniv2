<?php

namespace App\Filament\Resources\AffiliateMarketingAssets\Pages;

use App\Filament\Resources\AffiliateMarketingAssets\AffiliateMarketingAssetResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAffiliateMarketingAsset extends EditRecord
{
    protected static string $resource = AffiliateMarketingAssetResource::class;

    private ?string $previousFilePath = null;

    private ?string $previousThumbnailPath = null;

    protected function beforeSave(): void
    {
        $this->previousFilePath = $this->record->file_path;
        $this->previousThumbnailPath = $this->record->thumbnail_path;
    }

    protected function afterSave(): void
    {
        foreach ([
            [$this->previousFilePath, $this->record->file_path],
            [$this->previousThumbnailPath, $this->record->thumbnail_path],
        ] as [$previous, $current]) {
            if ($previous && $previous !== $current) {
                Storage::disk('local')->delete($previous);
            }
        }
    }
}
