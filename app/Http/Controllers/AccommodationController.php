<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Page;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 3)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $page->sections()
            ->where('is_active', true)
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $accommodationIds = [3, 4, 5, 6, 7];

        $accommodations = Accommodation::query()
            ->published()
            ->whereIn('id', $accommodationIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $accommodationIds) . ')')
            ->with([
                'activeImages',
                'features',
            ])
            ->get();

        return view('pages.accommodations.index', [
            'page' => $page,
            'sections' => $sections,
            'accommodations' => $accommodations,
        ]);
    }

    public function show(string $type, Accommodation $accommodation): View
    {
        abort_unless($accommodation->is_active, 404);

        abort_unless($type === $accommodation->url_prefix, 404);

        $accommodation->load([
            'activeImages',
            'features',
        ]);

        $relatedAccommodations = Accommodation::query()
            ->published()
            ->whereKeyNot($accommodation->id)
            ->with([
                'activeImages',
                'features',
            ])
            ->limit(6)
            ->get();

        return view('pages.accommodations.show', [
            'accommodation' => $accommodation,
            'relatedAccommodations' => $relatedAccommodations,
        ]);
    }
}
