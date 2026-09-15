<?php

namespace App\Http\Controllers;

use App\Models\SpaSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SpaLandingController extends Controller
{
    public function __invoke(): View
    {
        $spaSettings = Schema::hasTable('spa_settings') ? SpaSetting::query()->first() : null;

        return view('pages.spa-landing.index', [
            'spaSettings' => $spaSettings,
            'heroImage' => $this->resolveImage($spaSettings?->hero_image),
            'heroMobileImage' => $this->resolveImage($spaSettings?->hero_mobile_image ?: $spaSettings?->hero_image),
            'wellnessPhilosophyImage' => $this->resolveImage($spaSettings?->wellness_philosophy_image),
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

        return Storage::disk('public')->url($path);
    }
}
