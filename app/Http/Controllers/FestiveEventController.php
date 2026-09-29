<?php

namespace App\Http\Controllers;

use App\Models\FestiveEvent;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FestiveEventController extends Controller
{
    public function __invoke(FestiveEvent $festiveEvent): View
    {
        abort_unless($festiveEvent->is_active, 404);

        $menuItems = collect($festiveEvent->menu_items ?? [])->map(function (array $item): array {
            $item['image_url'] = $this->resolveImage($item['image'] ?? null);

            return $item;
        })->all();

        return view('pages.festive.show', [
            'event' => $festiveEvent,
            'heroImage' => $this->resolveImage($festiveEvent->hero_image),
            'menuItems' => $menuItems,
            'programmeImage' => $this->resolveImage($festiveEvent->programme_image),
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
