<?php

namespace App\Http\Controllers;

use App\Models\BlogNews;
use App\Models\Experience;
use App\Models\Offer;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::query()
            ->forMainSite()
            ->with([
                'sections' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
                'sections.images' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->where('slug', 'home')
            ->where('is_active', true)
            ->first();

        $featuredOffers = Offer::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where(function ($query) {
                $query
                    ->whereNull('valid_start_date')
                    ->orWhereDate('valid_start_date', '<=', today());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('valid_end_date')
                    ->orWhereDate('valid_end_date', '>=', today());
            })
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->limit(6)
            ->get();

        return view('pages.home', [
            'page' => $page,
            'featuredOffers' => $featuredOffers,
        ]);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $page = Page::query()
            ->forMainSite()
            ->with([
                'sections' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
                'sections.images' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $page) {
            return redirect()->route('home', [], 301);
        }

        return view('pages.show', [
            'page' => $page,
        ]);
    }

    public function explore(): View
    {
        $page = Page::query()
            ->where('id', 1)
            ->where('is_active', true)
            ->firstOrFail();

        $offers = Offer::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $experienceSlugs = [
            'moonlit-jungle-romance',
            'riverside-romance',
            'balinese-blessing-purification-at-the-holy-river',
            'nandini-signature-spa-on-the-river',
        ];

        $experienceOrder = collect($experienceSlugs)
            ->map(fn(string $slug, int $index): string => "WHEN ? THEN {$index}")
            ->implode(' ');

        $experiences = Experience::query()
            ->where('is_active', true)
            ->whereIn('slug', $experienceSlugs)
            ->orderByRaw("CASE slug {$experienceOrder} ELSE ? END", [
                ...$experienceSlugs,
                count($experienceSlugs),
            ])
            ->get();

        $blogNews = BlogNews::query()
            ->published()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('pages.explore', [
            'page' => $page,
            'offers' => $offers,
            'experiences' => $experiences,
            'blogNews' => $blogNews,
        ]);
    }
}
