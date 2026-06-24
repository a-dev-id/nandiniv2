<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AwardController extends Controller
{
    public function index(Request $request, int $page = 1): View|RedirectResponse
    {
        if ($request->query('page')) {
            $queryPage = max((int) $request->query('page'), 1);

            return redirect()->route(
                $queryPage === 1 ? 'awards.index' : 'awards.page',
                $queryPage === 1 ? [] : ['page' => $queryPage],
                301
            );
        }

        if ($page < 1) {
            abort(404);
        }

        $page = Page::query()
            ->where('id', 9)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $awards = Award::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(perPage: 10, page: $request->route('page') ? (int) $request->route('page') : 1);

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
