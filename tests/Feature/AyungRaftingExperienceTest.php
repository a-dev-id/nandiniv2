<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\ExperiencePrice;
use App\Models\Inquiry;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AyungRaftingExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ayung_rafting_uses_the_standard_experience_detail_page(): void
    {
        Page::query()->forceCreate([
            'id' => 5,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Experiences',
            'slug' => 'experiences',
            'title' => 'Experiences',
            'is_active' => true,
        ]);

        $category = ExperienceCategory::query()->create([
            'name' => 'Ubud Jungle Adventures',
            'slug' => 'ubud-jungle-adventures',
            'is_active' => true,
        ]);

        $experience = Experience::query()->create([
            'experience_category_id' => $category->id,
            'title' => 'Ayung White Water Rafting',
            'slug' => 'ayung-white-water-rafting',
            'description' => '<p>Ayung River rafting experience.</p>',
            'card_image' => 'experiences/cards/rafting.webp',
            'card_image_alt' => 'Ayung White Water Rafting | Bali River Adventure Experience',
            'is_active' => true,
            'meta_title' => 'Ayung White Water Rafting | Bali River Adventure Experience',
            'meta_description' => 'Experience an exhilarating white water rafting adventure on one of Bali’s most iconic rivers.',
        ]);

        ExperiencePrice::query()->create([
            'experience_id' => $experience->id,
            'price' => 995000,
            'currency' => 'IDR',
            'price_type' => 'plus_plus',
            'unit_type' => 'per_person',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Experience::query()->create([
            'experience_category_id' => $category->id,
            'title' => 'Another Experience',
            'slug' => 'another-experience',
            'is_active' => true,
        ]);

        $response = $this->get('https://'.config('domains.main').'/experience/ayung-white-water-rafting')->assertOk();

        $response
            ->assertViewIs('pages.experiences.show')
            ->assertSee('<title>Ayung White Water Rafting | Bali River Adventure Experience</title>', false)
            ->assertSee('Ayung White Water Rafting')
            ->assertSee('Inquire Now')
            ->assertSee('data-inquiry-button', false)
            ->assertSee('data-gtm-section="overview"', false)
            ->assertSee('data-gtm-section="related_content"', false)
            ->assertSee('data-show-dots="false"', false)
            ->assertSee('Other Experiences')
            ->assertDontSee('data-gtm-section="rafting_details"', false)
            ->assertDontSee('data-gtm-section="ayung_guide"', false)
            ->assertDontSee('data-gtm-section="booking_cta"', false)
            ->assertDontSee('SEND A RAFTING INQUIRY');
    }

    public function test_shared_inquiry_endpoint_accepts_rafting_context_and_returns_success_state(): void
    {
        Http::fake();
        config([
            'services.recaptcha.enabled' => false,
            'services.email_relay.url' => 'https://relay.example.test/send',
            'services.email_relay.token' => 'test-token',
        ]);

        $payload = [
            'title' => 'Ms',
            'first_name' => 'Test',
            'last_name' => 'Guest',
            'email' => 'guest@example.com',
            'country' => 'Indonesia',
            'phone_code' => '+62',
            'phone' => '8123456789',
            'inquiry_title' => 'Ayung White Water Rafting',
            'inquiry_image' => 'https://nandinibali.com/storage/experiences/cards/rafting.webp',
            'reserve_date' => now()->addWeek()->toDateString(),
            'reserve_time' => '10:00',
            'source_url' => 'https://nandinibali.com/experience/ayung-white-water-rafting',
        ];

        $this->postJson('https://'.config('domains.main').'/inquiries', $payload)
            ->assertOk()
            ->assertJsonPath('message', 'Thank you. Your inquiry has been sent.');

        $inquiry = Inquiry::query()->latest('id')->firstOrFail();
        $this->assertSame('Ayung White Water Rafting', $inquiry->inquiry_title);
        $this->assertSame('10:00', $inquiry->reserve_time);
        $this->assertNull($inquiry->experience_id);
    }
}
