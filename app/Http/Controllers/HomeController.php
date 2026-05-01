<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Page;
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

        $sections = $page->sections()
            ->where('is_active', true)
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('pages.home', [
            'page' => $page,
            'offers' => $offers,
            'sections' => $sections,
        ]);
    }
}
