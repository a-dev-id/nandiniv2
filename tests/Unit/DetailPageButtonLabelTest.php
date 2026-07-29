<?php

namespace Tests\Unit;

use App\Support\DetailPageButtonLabel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DetailPageButtonLabelTest extends TestCase
{
    #[DataProvider('detailDestinations')]
    public function test_it_uses_more_details_for_detail_destinations(?string $routeName, ?string $url): void
    {
        $this->assertSame(
            'More Details',
            DetailPageButtonLabel::resolve('Discover', $routeName, $url),
        );
    }

    public static function detailDestinations(): array
    {
        return [
            'named show route' => ['offers.show', '/offers'],
            'offer URL' => [null, '/offer/stay-longer-save-more'],
            'experience URL' => [null, 'https://nandinibali.com/experience/cooking-class'],
            'villa URL' => [null, '/jungle-villas/jungle-view-villa'],
            'suite URL' => [null, '/the-royal-suite/presidential-royal-suite'],
            'honeymoon URL' => [null, '/honeymoon/honeymoon-packages-4-days-3-nights'],
            'spa URL' => [null, '/spa-wellness/exotic-spa-treatment'],
            'holy river URL' => [null, '/holy-river/riverside-bliss'],
            'blog URL' => [null, '/blog-news/a-jungle-story'],
        ];
    }

    #[DataProvider('nonDetailDestinations')]
    public function test_it_keeps_the_existing_label_for_non_detail_destinations(?string $routeName, ?string $url): void
    {
        $this->assertSame(
            'Explore More',
            DetailPageButtonLabel::resolve('Explore More', $routeName, $url),
        );
    }

    public static function nonDetailDestinations(): array
    {
        return [
            'listing route' => ['offers.index', '/offers'],
            'experience category' => ['experiences.category', '/experiences/culinary-journeys'],
            'spa listing' => [null, '/spa-wellness'],
            'booking action' => [null, 'https://book-directonline.com/properties/nandini'],
            'WhatsApp action' => [null, 'https://wa.me/6281236871170'],
        ];
    }
}
