<?php

namespace App\Services\Affiliate;

use App\Models\Affiliate;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class UpdatePendingAffiliateService
{
    public function __construct(
        private readonly AffiliateProfileNormalizer $normalizer,
        private readonly AffiliateAuditService $audit,
    ) {}

    public function update(Affiliate $affiliate, array $data, User $actor): Affiliate
    {
        $data = $this->normalizer->normalize($data);

        return DB::transaction(function () use ($affiliate, $data, $actor): Affiliate {
            $locked = Affiliate::query()->lockForUpdate()->findOrFail($affiliate->getKey());

            if (! $locked->isPending()) {
                throw new DomainException('Only a complete pending affiliate can be edited.');
            }

            $emailChanged = $locked->email !== $data['email'];
            $locked->update(collect($data)->only([
                'name', 'email', 'phone_whatsapp', 'instagram', 'facebook', 'tiktok', 'x', 'threads',
            ])->all());

            if ($emailChanged) {
                $this->audit->record($locked, 'email_changed', $actor);
            }

            $this->audit->record($locked, 'contact_profile_updated', $actor);

            return $locked;
        }, 3);
    }
}
