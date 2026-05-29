<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class AboutUsController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 22)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        return view('pages.about-us', [
            'page' => $page,
            'sections' => $sections,
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
