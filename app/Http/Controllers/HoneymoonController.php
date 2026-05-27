<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
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

        $offers = Offer::query()
            ->published()
            ->where(function ($query) {
                $query
                    ->where('title', 'like', '%honeymoon%')
                    ->orWhere('slug', 'like', '%honeymoon%')
                    ->orWhere('excerpt', 'like', '%honeymoon%')
                    ->orWhere('description', 'like', '%honeymoon%');
            })
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get();

        return view('pages.honeymoon.index', [
            'page' => $page,
            'sections' => $sections,
            'offers' => $offers,
        ]);
    }

    public function show(Offer $offer): View
    {
        $this->abortIfOfferIsNotPublished($offer);

        $page = Page::query()
            ->where('id', 7)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $relatedOffers = Offer::query()
            ->published()
            ->whereKeyNot($offer->id)
            ->where(function ($query) {
                $query
                    ->where('title', 'like', '%honeymoon%')
                    ->orWhere('slug', 'like', '%honeymoon%')
                    ->orWhere('excerpt', 'like', '%honeymoon%')
                    ->orWhere('description', 'like', '%honeymoon%');
            })
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get();

        return view('pages.honeymoon.show', [
            'page' => $page,
            'sections' => $sections,
            'offer' => $offer,
            'relatedOffers' => $relatedOffers,
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

    protected function abortIfOfferIsNotPublished(Offer $offer): void
    {
        $today = today();

        $title = strtolower((string) $offer->title);
        $slug = strtolower((string) $offer->slug);
        $excerpt = strtolower((string) $offer->excerpt);
        $description = strtolower(strip_tags((string) $offer->description));

        $isHoneymoonOffer =
            str_contains($title, 'honeymoon')
            || str_contains($slug, 'honeymoon')
            || str_contains($excerpt, 'honeymoon')
            || str_contains($description, 'honeymoon');

        $isPublished =
            $offer->is_active
            && (blank($offer->valid_start_date) || $offer->valid_start_date->lte($today))
            && (blank($offer->valid_end_date) || $offer->valid_end_date->gte($today))
            && $isHoneymoonOffer;

        abort_unless($isPublished, 404);
    }
}
