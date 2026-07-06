<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 3)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

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

    public function villas(): View
    {
        $page = Page::query()
            ->where('slug', 'jungle-villas')
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $accommodations = Accommodation::query()
            ->published()
            ->where('accommodation_type', 'villa')
            ->with([
                'activeImages',
                'features',
            ])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('pages.accommodations.jungle-villas', [
            'page' => $page,
            'sections' => $sections,
            'accommodations' => $accommodations,
        ]);
    }

    public function suites(): View
    {
        $page = Page::query()
            ->where('slug', 'the-royal-suites')
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $accommodations = Accommodation::query()
            ->published()
            ->where('accommodation_type', 'suite')
            ->with([
                'activeImages',
                'features',
            ])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('pages.accommodations.the-royal-suite', [
            'page' => $page,
            'sections' => $sections,
            'accommodations' => $accommodations,
        ]);
    }

    public function presidentialRoyalSuite(): View
    {
        $page = Page::query()
            ->where('slug', 'presidential-royal-suite')
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $accommodation = Accommodation::query()
            ->published()
            ->where('slug', 'presidential-royal-suite')
            ->with([
                'activeImages',
                'features',
            ])
            ->firstOrFail();

        $relatedAccommodations = Accommodation::query()
            ->published()
            ->whereKeyNot($accommodation->id)
            ->with([
                'activeImages',
                'features',
            ])
            ->inRandomOrder()
            ->get();

        return view('pages.accommodations.presidential-royal-suite', [
            'page' => $page,
            'sections' => $sections,
            'accommodation' => $accommodation,
            'relatedAccommodations' => $relatedAccommodations,
        ]);
    }

    public function show(string $type, Accommodation $accommodation): View|RedirectResponse
    {
        if (! $accommodation->is_active) {
            return $this->redirectToAccommodationRoot($type);
        }

        if ($type !== $accommodation->url_prefix) {
            return $this->redirectToAccommodationRoot($type);
        }

        if ($accommodation->slug === 'presidential-royal-suite') {
            return redirect()->route('accommodations.presidential-royal-suite.show', [], 301);
        }

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
            ->inRandomOrder()
            ->get();

        return view('pages.accommodations.show', [
            'accommodation' => $accommodation,
            'relatedAccommodations' => $relatedAccommodations,
        ]);
    }

    private function getPageSections(Page $page)
    {
        return $page->sections()
            ->where('is_active', true)
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
    }

    private function redirectToAccommodationRoot(string $type): RedirectResponse
    {
        return redirect()->route(
            $type === 'the-royal-suites'
                ? 'accommodations.suites'
                : 'accommodations.villas',
            [],
            301
        );
    }
}
