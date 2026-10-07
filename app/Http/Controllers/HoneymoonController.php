<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Honeymoon;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HoneymoonController extends Controller
{
    private const LANDING_SECTION_KEYS = [
        'honeymoon_hero',
        'honeymoon_intro',
        'honeymoon_features',
        'honeymoon_accommodations',
        'honeymoon_package',
        'honeymoon_dining',
        'honeymoon_spa',
        'honeymoon_itinerary',
        'honeymoon_celebrations',
        'honeymoon_faq',
        'honeymoon_final_cta',
    ];

    public function index(): View
    {
        $page = Page::query()
            ->where('id', 7)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);
        $usesHoneymoonSections = $page->sections()
            ->whereIn('section_key', self::LANDING_SECTION_KEYS)
            ->exists();

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

        $accommodationOrder = [
            'panoramic-jungle-view-villa',
            'private-garden-royal-suite',
            'panoramic-corner-jacuzzi-royal-suite',
        ];

        $accommodations = Accommodation::query()
            ->published()
            ->whereIn('slug', $accommodationOrder)
            ->get()
            ->sortBy(fn (Accommodation $accommodation) => array_search(
                $accommodation->slug,
                $accommodationOrder,
                true
            ))
            ->values();

        return view('pages.honeymoon.index', [
            'page' => $page,
            'sections' => $sections,

            // Main variable
            'honeymoons' => $honeymoons,
            'featuredHoneymoon' => $honeymoons->firstWhere('is_featured', true) ?: $honeymoons->first(),
            'accommodations' => $accommodations,
            'usesHoneymoonSections' => $usesHoneymoonSections,

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
