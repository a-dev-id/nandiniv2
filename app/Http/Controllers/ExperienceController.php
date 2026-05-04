<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Page;
use Illuminate\View\View;

class ExperienceController extends Controller
{
    public function index(): View
    {
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
        ]);
    }

    public function show(Experience $experience): View
    {
        abort_if(! $experience->is_active, 404);

        abort_if(
            $experience->category && $experience->category->slug === 'holy-river',
            404
        );

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
