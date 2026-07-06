<?php

namespace App\Http\Controllers;

use App\Models\Honeymoon;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HoneymoonController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 7)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $honeymoons = Honeymoon::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $today = today()->toDateString();

                $query
                    ->whereNull('valid_start_date')
                    ->orWhereDate('valid_start_date', '<=', $today);
            })
            ->where(function ($query) {
                $today = today()->toDateString();

                $query
                    ->whereNull('valid_end_date')
                    ->orWhereDate('valid_end_date', '>=', $today);
            })
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get();

        return view('pages.honeymoon.index', [
            'page' => $page,
            'sections' => $sections,

            // Main variable
            'honeymoons' => $honeymoons,

            // Keep this if your current blade still uses $offers
            'offers' => $honeymoons,
        ]);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $honeymoon = Honeymoon::query()
            ->where('slug', $slug)
            ->first();

        if (! $honeymoon || ! $this->isHoneymoonPublished($honeymoon)) {
            return redirect()->route('honeymoon.index', [], 301);
        }

        $page = Page::query()
            ->where('id', 7)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $relatedHoneymoons = Honeymoon::query()
            ->where('is_active', true)
            ->whereKeyNot($honeymoon->id)
            ->where(function ($query) {
                $today = today()->toDateString();

                $query
                    ->whereNull('valid_start_date')
                    ->orWhereDate('valid_start_date', '<=', $today);
            })
            ->where(function ($query) {
                $today = today()->toDateString();

                $query
                    ->whereNull('valid_end_date')
                    ->orWhereDate('valid_end_date', '>=', $today);
            })
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get();

        return view('pages.honeymoon.show', [
            'page' => $page,
            'sections' => $sections,

            // Main variable
            'honeymoon' => $honeymoon,
            'relatedHoneymoons' => $relatedHoneymoons,

            // Keep these if your current blade still uses offer variables
            'offer' => $honeymoon,
            'relatedOffers' => $relatedHoneymoons,
        ]);
    }

    private function getPageSections(Page $page): Collection
    {
        return $page->sections()
            ->where('is_active', true)
            ->with([
                'images' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();
    }

    protected function isHoneymoonPublished(Honeymoon $honeymoon): bool
    {
        $today = today();

        return $honeymoon->is_active
            && (blank($honeymoon->valid_start_date) || $honeymoon->valid_start_date->lte($today))
            && (blank($honeymoon->valid_end_date) || $honeymoon->valid_end_date->gte($today));
    }
}
