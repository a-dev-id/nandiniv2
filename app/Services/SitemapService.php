<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\BlogNews;
use App\Models\DiningExperience;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\FestiveEvent;
use App\Models\Honeymoon;
use App\Models\Offer;
use App\Models\Page;
use App\Models\SignatureDish;
use App\Models\Spa;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SitemapService
{
    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    public function urls(?string $host = null): Collection
    {
        $host ??= config('domains.main');

        return match ($host) {
            config('domains.dining') => $this->diningUrls(),
            config('domains.spa') => $this->spaSiteUrls(),
            config('domains.voucher') => $this->voucherUrls(),
            config('domains.affiliate') => $this->affiliateUrls(),
            default => $this->mainSiteUrls(),
        };
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function mainSiteUrls(): Collection
    {
        return collect()
            ->merge($this->staticUrls())
            ->merge($this->pageUrls())
            ->merge($this->offerUrls())
            ->merge($this->blogUrls())
            ->merge($this->accommodationUrls())
            ->merge($this->experienceUrls())
            ->merge($this->honeymoonUrls())
            ->merge($this->festiveEventUrls())
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
            ['route' => 'wedding.index', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'sustainability.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'about-us.index', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'blog.index', 'changefreq' => 'daily', 'priority' => '0.8'],
            ['route' => 'awards.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'events.index', 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['route' => 'festive.index', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['route' => 'gallery.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'guest-reviews.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['route' => 'faq.index', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['route' => 'contact.index', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        if (! config('features.disable_membership_feature')) {
            $urls[] = ['route' => 'membership.index', 'changefreq' => 'monthly', 'priority' => '0.7'];
            $urls[] = ['route' => 'membership.benefits', 'changefreq' => 'monthly', 'priority' => '0.6'];
            $urls[] = ['route' => 'membership.privilege-redemption', 'changefreq' => 'weekly', 'priority' => '0.6'];
        }

        return collect($urls)
            ->map(fn (array $url) => $this->entry($url['url'] ?? route($url['route']), null, $url['changefreq'], $url['priority']))
            ->all();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function pageUrls(): Collection
    {
        return Page::query()
            ->forMainSite()
            ->where('is_active', true)
            ->where('include_in_sitemap', true)
            ->whereNot('slug', 'home')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['slug', 'updated_at'])
            ->map(fn (Page $page) => $this->entry(
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
            ->map(fn (Offer $offer) => $this->entry(route('offers.show', $offer->slug), $offer->updated_at, 'weekly', '0.8'));
    }

    private function blogUrls(): Collection
    {
        return BlogNews::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get(['slug', 'published_at', 'updated_at'])
            ->map(fn (BlogNews $blog) => $this->entry(
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
            ->reject(fn (Accommodation $accommodation) => $accommodation->slug === 'presidential-royal-suite')
            ->map(fn (Accommodation $accommodation) => $this->entry(
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
            ->map(fn (ExperienceCategory $category) => $this->entry(
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

        return $categoryUrls->toBase()->merge($experienceUrls->toBase());
    }

    private function honeymoonUrls(): Collection
    {
        return Honeymoon::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get(['slug', 'updated_at'])
            ->map(fn (Honeymoon $honeymoon) => $this->entry(route('honeymoon.show', $honeymoon->slug), $honeymoon->updated_at, 'weekly', '0.7'));
    }

    private function spaUrls(): Collection
    {
        return Spa::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get(['slug', 'updated_at'])
            ->map(fn (Spa $spa) => $this->entry(route('spa-landing.treatments.show', $spa->slug), $spa->updated_at, 'weekly', '0.7'));
    }

    private function festiveEventUrls(): Collection
    {
        return FestiveEvent::query()
            ->published()
            ->orderBy('sort_order')
            ->get(['slug', 'updated_at'])
            ->map(fn (FestiveEvent $event) => $this->entry(
                route('festive.show', $event),
                $event->updated_at,
                'weekly',
                '0.7'
            ));
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function diningUrls(): Collection
    {
        $experiences = DiningExperience::query()
            ->published()
            ->inDisplayOrder()
            ->get(['slug', 'updated_at'])
            ->map(fn (DiningExperience $experience) => $this->entry(
                route('dining-landing.experiences.show', $experience->slug),
                $experience->updated_at,
                'monthly',
                '0.7'
            ));

        $signatureDishes = SignatureDish::query()
            ->published()
            ->inDisplayOrder()
            ->get(['slug', 'updated_at'])
            ->map(fn (SignatureDish $dish) => $this->entry(
                route('dining-landing.signature-dishes.show', $dish->slug),
                $dish->updated_at,
                'monthly',
                '0.7'
            ));

        return collect([
            $this->entry(route('dining-landing.index'), null, 'weekly', '1.0'),
        ])
            ->merge($experiences)
            ->merge($signatureDishes)
            ->unique('loc')
            ->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function spaSiteUrls(): Collection
    {
        $pages = Page::query()
            ->forSpaSite()
            ->where('is_active', true)
            ->where('include_in_sitemap', true)
            ->whereNot('slug', 'home')
            ->orderBy('sort_order')
            ->get(['slug', 'updated_at'])
            ->map(fn (Page $page) => $this->entry(
                route('spa-landing.pages.show', $page->slug),
                $page->updated_at,
                'monthly',
                '0.7'
            ));

        return collect([
            $this->entry(route('spa-landing.index'), null, 'weekly', '1.0'),
        ])
            ->merge($pages)
            ->merge($this->spaUrls())
            ->unique('loc')
            ->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function voucherUrls(): Collection
    {
        $categories = VoucherCategory::query()
            ->active()
            ->ordered()
            ->get(['slug', 'updated_at'])
            ->map(fn (VoucherCategory $category) => $this->entry(
                route('voucher.category.show', $category),
                $category->updated_at,
                'weekly',
                '0.7'
            ));

        $vouchers = Voucher::query()
            ->active()
            ->ordered()
            ->get(['id', 'slug', 'selling_price', 'discount_percentage', 'is_active', 'updated_at'])
            ->filter(fn (Voucher $voucher) => $voucher->purchasable)
            ->map(fn (Voucher $voucher) => $this->entry(
                route('voucher.show', $voucher),
                $voucher->updated_at,
                'weekly',
                '0.8'
            ));

        return collect([
            $this->entry(route('voucher.index'), null, 'weekly', '1.0'),
        ])
            ->merge($categories)
            ->merge($vouchers)
            ->unique('loc')
            ->values();
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    private function affiliateUrls(): Collection
    {
        return collect([
            $this->entry(route('affiliate.landing'), null, 'monthly', '1.0'),
        ]);
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
