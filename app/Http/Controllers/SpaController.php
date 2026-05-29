<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Spa;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SpaController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->where('id', 6)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $spas = Spa::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get()
            ->map(function (Spa $spa) {
                $spa->setAttribute('show_url', route('spa.show', $spa->slug));
                $spa->setAttribute('booking_url', $this->resolveBookingUrl($spa));

                return $spa;
            });

        return view('pages.spa.index', [
            'page' => $page,
            'sections' => $sections,
            'spas' => $spas,
        ]);
    }

    public function show(string $slug): View
    {
        $spa = Spa::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $spa->setAttribute('show_url', route('spa.show', $spa->slug));
        $spa->setAttribute('booking_url', $this->resolveBookingUrl($spa));

        $page = Page::query()
            ->where('id', 6)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        $relatedSpas = Spa::query()
            ->published()
            ->whereKeyNot($spa->id)
            ->orderBy('sort_order')
            ->orderByDesc('valid_start_date')
            ->get()
            ->map(function (Spa $relatedSpa) {
                $relatedSpa->setAttribute('show_url', route('spa.show', $relatedSpa->slug));
                $relatedSpa->setAttribute('booking_url', $this->resolveBookingUrl($relatedSpa));

                return $relatedSpa;
            });

        return view('pages.spa.show', [
            'page' => $page,
            'sections' => $sections,
            'spa' => $spa,
            'relatedSpas' => $relatedSpas,
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

    private function resolveBookingUrl(Spa $spa): ?string
    {
        if (! empty($spa->booking_url_override)) {
            return $spa->booking_url_override;
        }

        if (! empty($spa->button_url)) {
            return $spa->button_url;
        }

        $checkinDate = $this->resolveBookingCheckinDate($spa);

        $query = array_filter([
            'checkin' => $checkinDate,
            'nights' => $spa->booking_nights,
            'rooms' => $spa->booking_rooms,
            'adults' => $spa->booking_adults,
            'rate' => $spa->booking_rate_code,
            'bkcode' => $spa->booking_bkcode,
        ], fn($value) => filled($value));

        if (empty($query)) {
            return null;
        }

        return 'https://nandinijunglebyhanginggardens.reserve-online.net/'
            . '?' . http_build_query($query);
    }

    private function resolveBookingCheckinDate(Spa $spa): ?string
    {
        if (empty($spa->booking_checkin_date)) {
            return null;
        }

        $checkinDate = $spa->booking_checkin_date instanceof Carbon
            ? $spa->booking_checkin_date
            : Carbon::parse($spa->booking_checkin_date);

        if ($checkinDate->isPast()) {
            return today()->toDateString();
        }

        return $checkinDate->toDateString();
    }
}
