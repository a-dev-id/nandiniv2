<?php

namespace App\Http\Controllers;

use App\Models\DiningExperience;
use App\Models\DiningSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DiningExperienceController extends Controller
{
    public function __invoke(string $experience): View
    {
        $managedExperience = Schema::hasTable('dining_experiences')
            ? DiningExperience::query()
                ->where('is_active', true)
                ->where('slug', $experience)
                ->with(['gallery' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->first()
            : null;

        abort_unless($managedExperience, 404);

        $diningExperience = [
            'key' => (string) $managedExperience->id,
            'slug' => $managedExperience->slug,
            'title' => $managedExperience->card_title,
            'description' => $managedExperience->short_description,
            'cta' => $managedExperience->card_cta_label,
            'alt' => $managedExperience->hero_image_alt,
        ];
        $image = $managedExperience->hero_image;
        if ($image && Storage::disk('public')->exists($image)) {
            $image = asset('storage/'.$image);
        } elseif ($image && (str_starts_with($image, 'http') || str_starts_with($image, '/'))) {
            $image = asset($image);
        } else {
            $image = null;
        }

        return view('pages.dining-landing.experience', [
            'experience' => $diningExperience,
            'cmsExperience' => $managedExperience,
            'image' => $image,
            'diningSettings' => DiningSetting::query()->first(),
        ]);
    }
}
