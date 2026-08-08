<?php

namespace App\Services\Affiliate;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateAffiliateService
{
    private const MAX_CODE_ATTEMPTS = 50;

    public function __construct(
        private readonly AffiliateProfileNormalizer $normalizer,
        private readonly AffiliateCodeGenerator $codes,
        private readonly AffiliateAuditService $audit,
        private readonly AffiliateNotificationService $notifications,
    ) {}

    public function create(
        array $data,
        AffiliateRegistrationSource $source,
        AffiliateStatus $status = AffiliateStatus::Pending,
        ?User $actor = null,
        ?string $password = null,
    ): Affiliate {
        if (! in_array($status, [AffiliateStatus::Pending, AffiliateStatus::Approved], true)) {
            throw new InvalidArgumentException('An affiliate can only be created as pending or approved.');
        }

        if ($source === AffiliateRegistrationSource::SelfRegistration && ($status !== AffiliateStatus::Pending || blank($password))) {
            throw new InvalidArgumentException('Self-registration requires a password and pending status.');
        }

        $data = $this->normalizer->normalize($data);
        $registeredAt = now();
        $baseCode = $this->codes->base($data['name'], $registeredAt);

        for ($attempt = 1; $attempt <= self::MAX_CODE_ATTEMPTS; $attempt++) {
            $code = $this->codes->candidate($baseCode, $attempt);

            try {
                return DB::transaction(function () use ($data, $source, $status, $actor, $password, $registeredAt, $code): Affiliate {
                    $approvedAt = $status === AffiliateStatus::Approved ? now() : null;
                    $affiliate = Affiliate::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => $password ?? Str::random(64),
                        'phone_whatsapp' => $data['phone_whatsapp'],
                        'instagram' => $data['instagram'],
                        'facebook' => $data['facebook'],
                        'tiktok' => $data['tiktok'],
                        'x' => $data['x'],
                        'threads' => $data['threads'],
                        'status' => $status,
                        'registration_source' => $source,
                        'created_by' => $source === AffiliateRegistrationSource::CreatedByNandini ? $actor?->getKey() : null,
                        'approved_by' => $approvedAt ? $actor?->getKey() : null,
                        'approved_at' => $approvedAt,
                        'affiliate_code' => $code,
                        'affiliate_code_generated_at' => $registeredAt,
                        'short_link_slug' => $code,
                        'short_link_activated_at' => $approvedAt,
                    ]);
                    $affiliate->assignRole(Role::AFFILIATE);

                    $this->audit->record($affiliate, $source === AffiliateRegistrationSource::SelfRegistration ? 'self_registration' : 'internal_creation', $actor);
                    $this->audit->record($affiliate, 'affiliate_code_generated', $actor, ['affiliate_code' => $code]);
                    $this->audit->record($affiliate, $status === AffiliateStatus::Approved ? 'approved_on_creation' : 'pending_status_assigned', $actor);

                    if ($source === AffiliateRegistrationSource::SelfRegistration) {
                        $this->notifications->afterCommitRegistration($affiliate);
                    } else {
                        $this->notifications->afterCommitInvitation($affiliate);
                    }

                    return $affiliate;
                }, 3);
            } catch (QueryException $exception) {
                if (! $this->isCodeCollision($exception) || $attempt === self::MAX_CODE_ATTEMPTS) {
                    throw $exception;
                }
            }
        }

        throw new InvalidArgumentException('Unable to generate a unique affiliate code.');
    }

    private function isCodeCollision(QueryException $exception): bool
    {
        $message = Str::lower($exception->getMessage());

        return str_contains($message, 'affiliate_code') || str_contains($message, 'short_link_slug');
    }
}
