<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class AwardController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 9)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $awards = Award::query()
            ->published()
            ->orderByRaw('award_year IS NULL')
            ->orderByRaw('CAST(award_year AS UNSIGNED) DESC')
            ->orderByDesc('award_date')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.awards', [
            'page' => $page,
            'sections' => $sections,
            'awards' => $awards,
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
