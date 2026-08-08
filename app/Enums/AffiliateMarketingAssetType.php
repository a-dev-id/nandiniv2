<?php

namespace App\Enums;

enum AffiliateMarketingAssetType: string
{
    case Image = 'image';
    case Video = 'video';
    case Banner = 'banner';
    case SocialMedia = 'social_media';
    case Document = 'document';
    case SpecialOffer = 'special_offer';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SocialMedia => 'Social Media',
            self::SpecialOffer => 'Offer',
            default => str($this->value)->replace('_', ' ')->title()->toString(),
        };
    }

    public function acceptsUpload(): bool
    {
        return $this !== self::Video;
    }
}
