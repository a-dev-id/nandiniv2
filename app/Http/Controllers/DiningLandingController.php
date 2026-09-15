<?php

namespace App\Http\Controllers;

use App\Models\GuestReview;
use App\Models\DiningSetting;
use App\Models\SignatureDish;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DiningLandingController extends Controller
{
    public function __invoke(): View
    {
        $diningSettings = Schema::hasTable('dining_settings') ? DiningSetting::query()->first() : null;
        $image = $this->resolveImage($diningSettings?->hero_image);

        $testimonials = GuestReview::query()
            ->published()
            ->forDining()
            ->orderBy('sort_order')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        $philosophyImage = $this->resolveImage($diningSettings?->philosophy_image);
        $signatureDish = Schema::hasTable('signature_dishes')
            ? SignatureDish::query()->published()->inDisplayOrder()->first()
            : null;

        return view('pages.dining-landing.index', [
            'heroImage' => $image,
            'testimonials' => $testimonials,
            'diningSettings' => $diningSettings,
            'philosophyImage' => $philosophyImage,
            'signatureDish' => $signatureDish,
        ]);
    }

    private function resolveImage(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return asset($path);
        }

        return Storage::disk('public')->exists($path) ? asset('storage/'.$path) : null;
    }
}
