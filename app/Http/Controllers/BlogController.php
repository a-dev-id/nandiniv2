<?php

namespace App\Http\Controllers;

use App\Models\BlogNews;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request, ?int $page = null): View|RedirectResponse
    {
        $queryPage = (int) $request->query('page', 0);

        if ($queryPage > 1) {
            return redirect()->route('blog.page', ['page' => $queryPage], 301);
        }

        if ($queryPage === 1 || $page === 1) {
            return redirect()->route('blog.index', [], 301);
        }

        $currentPage = max(1, $page ?? 1);

        $page = Page::query()
            ->where('id', 15)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $blogs = BlogNews::query()
            ->published()
            ->blog()
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->paginate(9, ['*'], 'page', $currentPage);

        return view('pages.blog-news.index', [
            'page' => $page,
            'sections' => $sections,
            'blogs' => $blogs,
        ]);
    }

    public function show(string $slug): View
    {
        $page = Page::query()
            ->where('id', 15)
            ->where('is_active', true)
            ->firstOrFail();

        $blog = BlogNews::query()
            ->published()
            ->blog()
            ->where('slug', $slug)
            ->with([
                'activeSections.images' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->firstOrFail();

        $relatedBlogs = BlogNews::query()
            ->published()
            ->blog()
            ->whereKeyNot($blog->id)
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('pages.blog-news.show', [
            'page' => $page,
            'blog' => $blog,
            'sections' => $blog->activeSections,
            'relatedBlogs' => $relatedBlogs,
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
