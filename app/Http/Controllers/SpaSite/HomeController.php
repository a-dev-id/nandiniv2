<?php

namespace App\Http\Controllers\SpaSite;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Spa;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::query()
            ->forSpaSite()
            ->with([
                'sections' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'sections.images' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            ])
            ->where('slug', 'home')
            ->where('is_active', true)
            ->firstOrFail();

        $spas = Spa::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->limit(4)
            ->get()
            ->each(function (Spa $spa): void {
                $spa->setAttribute('show_url', route('spa.show', $spa->slug));
            });

        return view('spa-site.home', [
            'page' => $page,
            'spas' => $spas,
        ]);
    }
}
