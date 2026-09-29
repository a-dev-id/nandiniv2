<?php

namespace App\Http\Controllers;

use App\Models\FestiveSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FestiveLandingController extends Controller
{
    public function __invoke(): View
    {
        $settings = Schema::hasTable('festive_settings')
            ? FestiveSetting::query()->first()
            : null;

        $celebrations = collect($settings?->celebrations ?? [])->map(function (array $celebration): array {
            $celebration['image_url'] = $this->resolveImage($celebration['image'] ?? null);

            return $celebration;
        })->all();

        return view('pages.festive.index', [
            'settings' => $settings,
            'heroImage' => $this->resolveImage($settings?->hero_image),
            'celebrations' => $celebrations,
            'bookingCtaImage' => $this->resolveImage($settings?->booking_cta_image),
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
