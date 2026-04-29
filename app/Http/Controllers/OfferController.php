<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Page;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('slug', 'offers')
            ->where('is_active', true)
            ->first();

        $offers = Offer::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get();

        return view('offers.index', [
            'page' => $page,
            'offers' => $offers,
        ]);
    }

    public function show(Offer $offer): View
    {
        $this->abortIfOfferIsNotPublished($offer);

        $relatedOffers = Offer::query()
            ->published()
            ->whereKeyNot($offer->id)
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->limit(3)
            ->get();

        return view('offers.show', [
            'offer' => $offer,
            'relatedOffers' => $relatedOffers,
        ]);
    }

    protected function abortIfOfferIsNotPublished(Offer $offer): void
    {
        $today = today();

        $isPublished =
            $offer->is_active
            && (blank($offer->valid_start_date) || $offer->valid_start_date->lte($today))
            && (blank($offer->valid_end_date) || $offer->valid_end_date->gte($today));

        abort_unless($isPublished, 404);
    }
}
