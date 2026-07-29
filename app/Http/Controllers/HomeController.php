<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\GuestReview;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 1)
            ->where('is_active', true)
            ->firstOrFail();

        $offers = Offer::query()
            ->published()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $villas = Accommodation::query()
            ->published()
            ->where('slug', '!=', 'presidential-royal-suite')
            ->get();

        $presidentialSuite = Accommodation::query()
            ->where('slug', 'presidential-royal-suite')
            ->where('is_active', true)
            ->with(['activeImages' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->first();

        $experienceCategories = ExperienceCategory::query()
            ->where('is_active', true)
            ->whereNot('slug', 'holy-river')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $diningSections = PageSection::query()
            ->where('page_id', 4)
            ->where('is_active', true)
            ->whereIn('title', [
                'Wild Ginger Restaurant',
                'Bar & Lounge',
                'Afternoon Tea in the Heart of the Jungle',
            ])
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->each(function (PageSection $section): void {
                $section->setAttribute('show_url', route('dining.index'));
            });

        $spaSections = PageSection::query()
            ->where('page_id', 6)
            ->where('is_active', true)
            ->whereIn('title', [
                'Spa Jacuzzi',
                'Spa Jaccuzi',
                'Wine Spa',
                'Spa by the River',
            ])
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->each(function (PageSection $section): void {
                $section->setAttribute('show_url', route('spa.index'));
            });

        $ubudJungleAdventures = Experience::query()
            ->where('is_active', true)
            ->where('slug', 'not like', '%day-pass%')
            ->whereRaw('LOWER(title) NOT LIKE ?', ['%day pass%'])
            ->whereHas('category', function ($query) {
                $query->where('slug', 'ubud-jungle-adventures');
            })
            ->with('category')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $sections = $page->sections()
            ->where('is_active', true)
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $guestReviews = GuestReview::query()
            ->published()
            ->featured()
            ->orderBy('sort_order')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        return view('pages.home', [
            'page' => $page,
            'offers' => $offers,
            'villas' => $villas,
            'presidentialSuite' => $presidentialSuite,
            'experienceCategories' => $experienceCategories,
            'diningSections' => $diningSections,
            'spaSections' => $spaSections,
            'ubudJungleAdventures' => $ubudJungleAdventures,
            'sections' => $sections,
            'guestReviews' => $guestReviews,
        ]);
    }
}
