<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Reward;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 35)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);
        $rewards = $this->getRandomActiveRewards();

        return view('pages.membership.index', [
            'page' => $page,
            'sections' => $sections,
            'rewards' => $rewards,
        ]);
    }

    public function benefits(): View
    {
        $page = Page::query()
            ->where('id', 39)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);
        $rewards = $this->getRandomActiveRewards();

        return view('pages.membership.benefit', [
            'page' => $page,
            'sections' => $sections,
            'rewards' => $rewards,
        ]);
    }

    public function privilegeRedemption(): View
    {
        $page = Page::query()
            ->where('id', 40)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);
        $rewards = $this->getAllActiveRewards();

        return view('pages.membership.privilege-redemption', [
            'page' => $page,
            'sections' => $sections,
            'rewards' => $rewards,
        ]);
    }

    public function dashboard(): View
    {
        $page = Page::query()
            ->where('id', 37)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);
        $rewards = $this->getDashboardRewards();

        return view('pages.membership.dashboard', [
            'page' => $page,
            'sections' => $sections,
            'rewards' => $rewards,
        ]);
    }

    private function getPageSections(Page $page): Collection
    {
        return $page->sections()
            ->where('is_active', true)
            ->with([
                'images' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->orderBy('sort_order')
            ->get();
    }

    private function getRandomActiveRewards(): Collection
    {
        return Reward::query()
            ->with('category')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }

    private function getDashboardRewards(): Collection
    {
        return Reward::query()
            ->with('category')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(9)
            ->get();
    }

    private function getAllActiveRewards(): Collection
    {
        return Reward::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }
}
