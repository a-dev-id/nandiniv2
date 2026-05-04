<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Page;
use Illuminate\View\View;

class HolyRiverController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 24)
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

        $experiences = Experience::query()
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('slug', 'holy-river');
            })
            ->with(['category', 'prices' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('pages.holy-river.index', [
            'page' => $page,
            'sections' => $sections,
            'experiences' => $experiences,
        ]);
    }

    public function show(string $slug): View
    {
        $experience = Experience::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('slug', 'holy-river');
            })
            ->with(['category', 'prices' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->firstOrFail();

        $page = Page::query()
            ->where('id', 24)
            ->where('is_active', true)
            ->with(['sections' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->where('section_key', 'intro_text_section')
                    ->with(['images' => function ($query) {
                        $query
                            ->where('is_active', true)
                            ->orderBy('sort_order');
                    }])
                    ->orderBy('sort_order');
            }])
            ->firstOrFail();

        $relatedExperiences = Experience::query()
            ->where('is_active', true)
            ->where('id', '!=', $experience->id)
            ->whereHas('category', function ($query) {
                $query->where('slug', 'holy-river');
            })
            ->with(['category', 'prices' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('pages.holy-river.show', [
            'page' => $page,
            'experience' => $experience,
            'relatedExperiences' => $relatedExperiences,
        ]);
    }
}
