<?php

namespace App\Support;

use App\Filament\Resources\Accommodations\AccommodationsResource;
use App\Filament\Resources\Awards\AwardResource;
use App\Filament\Resources\BlogNews\BlogNewsResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\GuestReviews\GuestReviewResource;
use App\Filament\Resources\Honeymoons\HoneymoonResource;
use App\Filament\Resources\MiniPopups\MiniPopupResource;
use App\Filament\Resources\Offers\OfferResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Spas\SpaResource;
use App\Models\AccommodationFeature;
use App\Models\AccommodationImage;
use App\Models\ExperienceCategory;
use App\Models\ExperiencePrice;
use App\Models\PageSection;
use App\Models\PageSectionImage;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class WebsiteContentAccess
{
    /** The explicit resource list also bounds route access, including resources sharing a model. */
    public static function resources(): array
    {
        return [
            PageResource::class,
            AccommodationsResource::class,
            ExperienceResource::class,
            SpaResource::class,
            HoneymoonResource::class,
            OfferResource::class,
            EventResource::class,
            BlogNewsResource::class,
            GalleryResource::class,
            GuestReviewResource::class,
            AwardResource::class,
            MiniPopupResource::class,
        ];
    }

    public static function isEditor(?User $user): bool
    {
        return $user && ! $user->hasRole(Role::ADMINISTRATOR) && $user->hasRole(Role::WEBSITE_CONTENT_EDITOR);
    }

    public static function allows(User $user, mixed $model): bool
    {
        $models = array_map(fn (string $resource): string => $resource::getModel(), self::resources());
        $models = [...$models, PageSection::class, PageSectionImage::class,
            AccommodationImage::class, AccommodationFeature::class,
            ExperiencePrice::class, ExperienceCategory::class];

        return $user->hasPermissionTo(Permission::WEBSITE_CONTENT_MANAGE)
            && in_array(is_object($model) ? $model::class : $model, $models, true);
    }
}
