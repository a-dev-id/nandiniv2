<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class SpaLandingPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->forSpaSite()
            ->with([
                'sections' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'sections.images' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.spa-landing.show', [
            'page' => $page,
        ]);
    }
}
