<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyAccommodationRedirectTest extends TestCase
{
    public function test_old_accommodation_page_permanently_redirects_to_jungle_villas(): void
    {
        $this->get('http://nandinibali.test/the-royal-suites-and-jungle-villas')
            ->assertStatus(301)
            ->assertRedirect('/jungle-villas');
    }
}
