<?php

namespace App\Support;

use App\Models\Offer;
use App\Models\Page;
use Illuminate\Support\Collection;

final class OfferStructuredData
{
    public static function listing(Page $page, Collection $offers): array
    {
        return SeoStructuredData::collection(SeoStructuredData::MAIN.'/offers', $page->title, $page->meta_description,
            $offers->map(fn (Offer $offer): array => [
                'name' => $offer->title,
                'url' => self::url($offer),
                'item' => ['@type' => 'WebPage', '@id' => self::url($offer).'#webpage', 'url' => self::url($offer)],
            ])->all());
    }

    public static function detail(Offer $offer): array
    {
        $url = self::url($offer);
        $page = SeoStructuredData::page($url, $offer->title, $offer->meta_description ?: $offer->excerpt);
        unset($page['@context']);
        $graph = [$page];
        $today = today();
        if ($offer->is_active
            && (! $offer->valid_start_date || $offer->valid_start_date->lte($today))
            && (! $offer->valid_end_date || $offer->valid_end_date->gte($today))) {
            $graph[0]['mainEntity'] = ['@id' => $url.'#offer'];
            $graph[] = array_filter([
                '@type' => 'Offer',
                '@id' => $url.'#offer',
                'url' => $url,
                'name' => $offer->title,
                'description' => SeoStructuredData::text($offer->description ?: $offer->excerpt),
                'image' => SeoStructuredData::image($offer->hero_image ?: $offer->card_image),
                'offeredBy' => SeoStructuredData::hotel(),
                // CMS validity dates control publication, not booking or stay dates.
                // Booking nights are URL defaults, not minimum-stay restrictions.
                'potentialAction' => self::bookingAction($offer),
            ], fn ($value): bool => $value !== null && $value !== '');
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }

    private static function bookingAction(Offer $offer): ?array
    {
        // Avoid the model's legacy fallback URL and member-specific voucher links.
        $target = $offer->button_url;
        // A custom CTA may be an enquiry or another page rather than a reservation.
        if (filled($target) && ! in_array(parse_url($target, PHP_URL_HOST), [
            'nandinijunglebyhanginggardens.reserve-online.net',
            'book-directonline.com',
        ], true)) {
            return null;
        }
        if (blank($target) && (filled($offer->booking_url_override) || filled($offer->booking_rate_code) || filled($offer->booking_bkcode))) {
            $target = $offer->booking_url;
        }
        $target = html_entity_decode((string) $target, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (! filter_var($target, FILTER_VALIDATE_URL) || ! in_array(parse_url($target, PHP_URL_SCHEME), ['https', 'http'], true)) {
            return null;
        }

        return ['@type' => 'ReserveAction', 'target' => $target];
    }

    private static function url(Offer $offer): string
    {
        return SeoStructuredData::MAIN.'/offer/'.$offer->slug;
    }
}
