<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\BlogNews;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\Honeymoon;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Spa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SitemapService
{
    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    public function urls(): Collection
    {
        return collect()
            ->merge($this->staticUrls())
            ->merge($this->pageUrls())
            ->merge($this->offerUrls())
            ->merge($this->blogUrls())
            ->merge($this->accommodationUrls())
            ->merge($this->experienceUrls())
            ->merge($this->honeymoonUrls())
            ->merge($this->spaUrls())
            ->unique('loc')
            ->values();
    }

    /**
     * @return array<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function staticUrls(): array
    {
        $urls = [
            ['route' => 'home', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['route' => 'explore', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'accommodations.index', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'accommodations.villas', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'accommodations.suites', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'accommodations.presidential-royal-suite.show', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'offers.index', 'changefreq' => 'daily', 'priority' => '0.9'],
            ['route' => 'experiences.index', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'holy-river.index', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'little-things.index', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'honeymoon.index', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'dining.index', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'spa.index', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'wedding.index', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'sustainability.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'about-us.index', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'blog.index', 'changefreq' => 'daily', 'priority' => '0.8'],
            ['route' => 'awards.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'gallery.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'faq.index', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['route' => 'contact.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        if (! config('features.disable_membership_feature')) {
            $urls[] = ['route' => 'membership.index', 'changefreq' => 'monthly', 'priority' => '0.7'];
            $urls[] = ['route' => 'membership.benefits', 'changefreq' => 'monthly', 'priority' => '0.6'];
            $urls[] = ['route' => 'membership.privilege-redemption', 'changefreq' => 'weekly', 'priority' => '0.6'];
            $urls[] = ['route' => 'membership.register', 'changefreq' => 'monthly', 'priority' => '0.5'];
        }

        return collect($urls)
            ->map(fn(array $url) => $this->entry(route($url['route']), null, $url['changefreq'], $url['priority']))
            ->all();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function pageUrls(): Collection
    {
        return Page::query()
            ->where('is_active', true)
            ->whereNot('slug', 'home')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['slug', 'updated_at'])
            ->map(fn(Page $page) => $this->entry(
                route('pages.show', $page->slug),
                $page->updated_at,
                'monthly',
                '0.6'
            ));
    }

    private function offerUrls(): Collection
    {
        return Offer::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get(['slug', 'updated_at'])
            ->map(fn(Offer $offer) => $this->entry(route('offers.show', $offer->slug), $offer->updated_at, 'weekly', '0.8'));
    }

    private function blogUrls(): Collection
    {
        return BlogNews::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get(['slug', 'published_at', 'updated_at'])
            ->map(fn(BlogNews $blog) => $this->entry(
                route('blog.show', $blog->slug),
                $blog->updated_at ?? $blog->published_at,
                'monthly',
                '0.7'
            ));
    }

    private function accommodationUrls(): Collection
    {
        return Accommodation::query()
            ->published()
            ->get(['slug', 'accommodation_type', 'updated_at'])
            ->reject(fn(Accommodation $accommodation) => $accommodation->slug === 'presidential-royal-suite')
            ->map(fn(Accommodation $accommodation) => $this->entry(
                route('accommodations.show', [
                    'type' => $accommodation->url_prefix,
                    'accommodation' => $accommodation->slug,
                ]),
                $accommodation->updated_at,
                'monthly',
                '0.7'
            ));
    }

    private function experienceUrls(): Collection
    {
        $categoryUrls = ExperienceCategory::query()
            ->where('is_active', true)
            ->whereNot('slug', 'holy-river')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['slug', 'updated_at'])
            ->map(fn(ExperienceCategory $category) => $this->entry(
                route('experiences.category', $category->slug),
                $category->updated_at,
                'monthly',
                '0.6'
            ));

        $experienceUrls = Experience::query()
            ->where('is_active', true)
            ->with('category:id,slug')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['id', 'experience_category_id', 'slug', 'updated_at'])
            ->map(function (Experience $experience) {
                $route = $experience->category?->slug === 'holy-river'
                    ? 'holy-river.show'
                    : 'experiences.show';

                return $this->entry(route($route, $experience->slug), $experience->updated_at, 'monthly', '0.7');
            });

        return $categoryUrls->merge($experienceUrls);
    }

    private function honeymoonUrls(): Collection
    {
        return Honeymoon::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get(['slug', 'updated_at'])
            ->map(fn(Honeymoon $honeymoon) => $this->entry(route('honeymoon.show', $honeymoon->slug), $honeymoon->updated_at, 'weekly', '0.7'));
    }

    private function spaUrls(): Collection
    {
        return Spa::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get(['slug', 'updated_at'])
            ->map(fn(Spa $spa) => $this->entry(route('spa.show', $spa->slug), $spa->updated_at, 'weekly', '0.7'));
    }

    private function entry(string $loc, Carbon|string|null $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $this->formatLastmod($lastmod),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function formatLastmod(Carbon|string|null $lastmod): ?string
    {
        if (blank($lastmod)) {
            return null;
        }

        return Carbon::parse($lastmod)->toDateString();
    }
}
