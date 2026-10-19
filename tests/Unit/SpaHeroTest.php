<?php

namespace Tests\Unit;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class SpaHeroTest extends TestCase
{
    public function test_mobile_booking_content_is_outside_clipped_media_with_one_heading(): void
    {
        $html = (string) $this->blade('<x-spa-landing.hero image="/test-spa.webp" />');
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $this->assertCount(1, $xpath->query('//h1'));
        $this->assertCount(0, $xpath->query('//div[contains(@class,"overflow-hidden")]//h1'));
        $this->assertCount(0, $xpath->query('//div[contains(@class,"overflow-hidden")]//a'));
        $this->assertStringContainsString('Experience restorative Balinese spa rituals surrounded by the tranquility of Nandini Jungle.', $html);
        $this->assertStringContainsString('data-gtm-section="hero"', $html);
        $this->assertStringContainsString('md:absolute md:inset-0 md:h-full', $html);
        $this->assertStringContainsString('md:min-h-[max(700px,100svh)] lg:min-h-[max(720px,100vh)]', $html);
        $this->assertStringContainsString('href="#treatments"', $html);
        $url = 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.');
        $this->assertStringContainsString('href="'.$url.'"', $html);
        $this->assertStringContainsString('min-h-11 w-full', $html);
    }
}
