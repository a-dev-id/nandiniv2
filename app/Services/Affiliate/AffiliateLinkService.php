<?php

namespace App\Services\Affiliate;

use App\Models\Affiliate;

class AffiliateLinkService
{
    public function shortLink(Affiliate|string $affiliate): string
    {
        $slug = $affiliate instanceof Affiliate ? $affiliate->short_link_slug : $affiliate;

        return config('domains.short_link_scheme').'://'.config('domains.short_link').'/'.$slug;
    }
}
