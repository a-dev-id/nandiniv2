<?php

namespace Tests\Feature;

use Tests\TestCase;

class NavbarMenuTest extends TestCase
{
    public function test_sidebar_navigation_uses_the_requested_order_and_more_group(): void
    {
        $response = $this->blade('<x-layouts.navbar />');

        $response->assertSeeInOrder([
            'data-menu-item="home"',
            'data-menu-item="festive-season"',
            'data-menu-item="accommodations"',
            'data-menu-item="holy-river"',
            'data-menu-item="little-things"',
            'data-menu-item="experiences"',
            'data-menu-item="offers"',
            'data-menu-item="events"',
            'data-menu-item="dining"',
            'data-menu-item="spa-wellness"',
            'data-menu-item="more"',
        ], false);

        $response->assertSeeInOrder([
            'data-more-menu-item="wedding"',
            'data-more-menu-item="sustainability"',
            'data-more-menu-item="gallery"',
            'data-more-menu-item="blog-news"',
            'data-more-menu-item="about-us"',
        ], false);
    }
}
