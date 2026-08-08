<?php

namespace App\Filament\Resources\AffiliateProgramSettings\Pages;

use App\Filament\Resources\AffiliateProgramSettings\AffiliateProgramSettingResource;
use App\Services\Affiliate\AffiliateAuditService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditAffiliateProgramSetting extends EditRecord
{
    protected static string $resource = AffiliateProgramSettingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $before = collect($data)->mapWithKeys(fn ($value, string $key): array => [$key => $record->getOriginal($key)])->all();
            $record->update($data);

            foreach ($data as $key => $value) {
                if ((string) ($before[$key] ?? '') === (string) $value) {
                    continue;
                }

                app(AffiliateAuditService::class)->record(null, 'affiliate_setting.changed', auth()->user(), [
                    'setting' => $key,
                    'previous' => $before[$key] ?? null,
                    'new' => $value,
                    'historical_snapshots_unchanged' => true,
                ], $record);
            }

            return $record;
        });
    }
}
