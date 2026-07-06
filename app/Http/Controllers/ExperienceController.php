<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExperienceController extends Controller
{
    public function index(Request $request, ?string $categorySlug = null): View|RedirectResponse
    {
        $queryCategory = $request->query('category');

        if (filled($queryCategory)) {
            return $queryCategory === 'all'
                ? redirect()->route('experiences.index', [], 301)
                : redirect()->route('experiences.category', ['categorySlug' => $queryCategory], 301);
        }

        if ($categorySlug !== null) {
            if ($categorySlug === 'holy-river') {
                return redirect()->route('holy-river.index', [], 301);
            }

            $category = ExperienceCategory::query()
                ->where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();

            if (! $category) {
                return redirect()->route('experiences.index', [], 301);
            }
        }

        $page = Page::query()
            ->where('id', 5)
            ->where('is_active', true)
            ->firstOrFail();

        $experiences = Experience::query()
            ->where('is_active', true)
            ->whereDoesntHave('category', function ($query) {
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

        return view('pages.experiences.index', [
            'page' => $page,
            'experiences' => $experiences,
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(Experience $experience): View|RedirectResponse
    {
        if (! $experience->is_active) {
            return redirect()->route('experiences.index', [], 301);
        }

        if ($experience->category && $experience->category->slug === 'holy-river') {
            return redirect()->route('holy-river.index', [], 301);
        }

        $experience->load(['category', 'prices' => function ($query) {
            $query
                ->where('is_active', true)
                ->orderBy('sort_order');
        }]);

        $page = Page::query()
            ->where('id', 5)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedExperiences = Experience::query()
            ->where('is_active', true)
            ->where('id', '!=', $experience->id)
            ->whereDoesntHave('category', function ($query) {
                $query->where('slug', 'holy-river');
            })
            ->with(['category', 'prices' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->inRandomOrder()
            ->get();

        return view('pages.experiences.show', [
            'page' => $page,
            'experience' => $experience,
            'relatedExperiences' => $relatedExperiences,
        ]);
    }
}
