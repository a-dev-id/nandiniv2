<?php

namespace App\Services\Affiliate;

use App\Models\Affiliate;
use App\Models\AffiliateAuditEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AffiliateAuditService
{
    public function record(?Affiliate $affiliate, string $event, User|Affiliate|null $actor = null, array $metadata = [], ?Model $subject = null): AffiliateAuditEvent
    {
        return AffiliateAuditEvent::query()->create([
            'affiliate_id' => $affiliate?->getKey(),
            'actor_user_id' => $actor instanceof User ? $actor->getKey() : null,
            'actor_affiliate_id' => $actor instanceof Affiliate ? $actor->getKey() : null,
            'event' => $event,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
