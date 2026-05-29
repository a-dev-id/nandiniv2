<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 10)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $galleries = Gallery::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('pages.gallery', [
            'page' => $page,
            'sections' => $sections,
            'galleries' => $galleries,
        ]);
    }

    private function getPageSections(Page $page): Collection
    {
        return $page->sections()
            ->where('is_active', true)
            ->with([
                'images' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
