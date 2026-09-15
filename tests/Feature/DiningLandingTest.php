<?php

namespace Tests\Feature;

use App\Http\Controllers\DiningController;
use App\Http\Controllers\HomeController;
use App\Models\GuestReview;
use App\Models\DiningSetting;
use App\Models\DiningExperience;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use App\Models\Experience;
use App\Models\Inquiry;
use App\Models\SignatureDish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiningLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dining_home_renders_shared_layout_hero_and_philosophy(): void
    {
        DiningSetting::query()->firstOrCreate()->update(['hero_image' => '/images/dining-approved.webp']);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertViewIs('pages.dining-landing.index')
            ->assertSee('id="mainNavbar"', false)
            ->assertSee('Copyright')
            ->assertSee('A Culinary Journey')
            ->assertSee('WhatsApp reservation')
            ->assertSee('https://dining.nandinibali.com/', false)
            ->assertSee('/images/dining-approved.webp')
            ->assertSee('https://www.youtube-nocookie.com/embed/GZav9hOJKts', false)
            ->assertSee('autoplay=1&amp;mute=1&amp;controls=0&amp;loop=1', false)
            ->assertSee('data-autoload-delay="600"', false)
            ->assertSee('min-h-[max(720px,100vh)]', false)
            ->assertSee('lg:px-[clamp(64px,5vw,100px)]', false)
            ->assertDontSee('max-w-7xl items-center px-6 pt-32', false)
            ->assertDontSee('coming soon')
            ->assertSee('id="dining-philosophy-title"', false)
            ->assertSee('More Than a Meal,')
            ->assertSee('A Meaningful Experience');
    }

    public function test_dining_subdomain_shows_mini_popup_and_floating_whatsapp(): void
    {
        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('nandini-mini-popup-closed-date', false)
            ->assertSee('aria-label="Chat with us on WhatsApp"', false)
            ->assertSee('Hi, how may we assist you today?');
    }

    public function test_main_routes_keep_their_existing_controllers(): void
    {
        foreach (['/' => HomeController::class, '/dining' => DiningController::class] as $path => $controller) {
            $route = Route::getRoutes()->match(Request::create('https://'.config('domains.main').$path));
            $this->assertSame($controller.'@index', $route->getActionName());
        }
    }

    public function test_main_website_dining_links_use_the_dining_subdomain(): void
    {
        $this->assertSame('https://dining.nandinibali.com/', config('dining.public_url'));
        $this->blade('<x-layouts.navbar />')
            ->assertSee('href="https://dining.nandinibali.com/"', false);
    }

    public function test_philosophy_content_uses_singleton_settings_and_preserves_safe_line_breaks(): void
    {
        DiningSetting::query()->firstOrCreate()->update([
            'philosophy_eyebrow' => 'A seasonal philosophy',
            'philosophy_heading' => "Fresh from Bali,\nMade with care <script>alert(1)</script>",
            'philosophy_description' => 'A description selected by the dining team.',
            'philosophy_accent_text' => "Flavours\nfrom our garden",
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('A seasonal philosophy')
            ->assertSee('Fresh from Bali,<br />', false)
            ->assertSee('Made with care &lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('A description selected by the dining team.')
            ->assertSee('Flavours<br />', false)
            ->assertSee('from our garden');
    }

    public function test_philosophy_uses_uploaded_image_and_alt_text(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('dining/philosophy/chef.webp', 'test-image');
        DiningSetting::query()->firstOrCreate()->update([
            'philosophy_image' => 'dining/philosophy/chef.webp',
            'philosophy_image_alt' => 'Chef plating a seasonal Balinese dish',
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('/storage/dining/philosophy/chef.webp', false)
            ->assertSee('alt="Chef plating a seasonal Balinese dish"', false);
    }

    public function test_signature_dish_uses_uploaded_background_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('dining/signature-dishes/rahang-tuna.webp', 'test-image');
        SignatureDish::query()->firstOrFail()->update([
            'image' => 'dining/signature-dishes/rahang-tuna.webp',
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('/storage/dining/signature-dishes/rahang-tuna.webp', false)
            ->assertDontSee('src="http://dining.nandinibali.test/dining/signature-dishes/rahang-tuna.webp"', false);
    }

    public function test_why_dine_content_and_order_come_from_singleton_settings(): void
    {
        DiningSetting::query()->firstOrCreate()->update([
            'why_dine_eyebrow' => 'Reasons to dine with us',
            'why_dine_heading' => "Remarkable food\nIn the rainforest",
            'why_dine_items' => [
                ['icon' => 'heart', 'title' => "Celebrations\nMade special", 'description' => "Intimate tables\nwith thoughtful service."],
                ['icon' => 'leaves', 'title' => 'Immersed in nature', 'description' => 'A peaceful jungle setting.'],
            ],
        ]);

        $response = $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('Reasons to dine with us')
            ->assertSee('Remarkable food<br />', false)
            ->assertSee('Celebrations<br />', false)
            ->assertSee('Intimate tables<br />', false)
            ->assertSee('lg:nth-[4n]:after:hidden', false);

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Immersed in nature'), strpos($content, 'Celebrations'));
    }

    public function test_why_dine_dynamic_text_is_escaped_and_unknown_icons_render_no_markup(): void
    {
        DiningSetting::query()->firstOrCreate()->update([
            'why_dine_items' => [[
                'icon' => '<script>alert(1)</script>',
                'title' => '<script>alert(2)</script>',
                'description' => '<img src=x onerror=alert(3)>',
            ]],
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(2)&lt;/script&gt;', false)
            ->assertSee('&lt;img src=x onerror=alert(3)&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<script>alert(2)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(3)>', false);
    }

    public function test_database_image_and_ctas_are_rendered(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('pages/hero/dining.webp', 'test-image');
        DiningSetting::query()->firstOrCreate()->update([
            'hero_image' => 'pages/hero/dining.webp',
            'hero_secondary_cta_label' => 'Database menu',
            'hero_secondary_cta_url' => 'https://nandinibali.com/menu.pdf',
            'hero_primary_cta_label' => 'Database reservation',
            'hero_primary_cta_url' => 'https://wa.me/6281236871170',
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('/storage/pages/hero/dining.webp')
            ->assertDontSee('Database menu')
            ->assertDontSee('https://nandinibali.com/menu.pdf', false)
            ->assertSee('https://wa.me/6281236871170', false)
            ->assertDontSee('Watch the dining experience')
            ->assertDontSee('coming soon');
    }

    public function test_cleared_database_values_do_not_restore_legacy_fallbacks(): void
    {
        DiningSetting::query()->firstOrCreate()->update([
            'hero_video_id' => null,
            'hero_image' => null,
            'hero_heading' => '',
            'hero_secondary_cta_label' => null,
            'hero_secondary_cta_url' => null,
            'reservation_cta_background_image' => null,
            'reservation_cta_label' => null,
            'reservation_cta_url' => null,
            'information_bar_items' => [['icon' => '', 'label' => 'No icon item', 'value' => 'Database only']],
            'faq_items' => [],
        ]);

        $this->get('http://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('No icon item')
            ->assertDontSee('https://i.ytimg.com/vi/GZav9hOJKts/maxresdefault.jpg', false)
            ->assertDontSee('A Culinary Journey')
            ->assertDontSee('may%20I%20view%20the%20dining%20menu', false)
            ->assertDontSee('/images/dining/romantic-dining-by-the-chapel.webp', false)
            ->assertDontSee('Do I need a reservation?');
    }

    public function test_guest_reviews_use_only_records_managed_as_dining_reviews_without_see_more_button(): void
    {
        GuestReview::query()->create([
            'reviewer_name' => 'Dining Guest',
            'review_text' => 'The restaurant food and attentive service made dinner memorable.',
            'excerpt' => 'The restaurant food and attentive service made dinner memorable.',
            'rating' => 5,
            'reviewed_at' => '2026-07-01',
            'source' => 'Google',
            'is_active' => true,
            'show_on_dining' => true,
            'sort_order' => 1,
        ]);
        GuestReview::query()->create([
            'reviewer_name' => 'General Website Guest',
            'review_text' => 'A romantic dining atmosphere with excellent wine and service.',
            'excerpt' => 'A romantic dining atmosphere with excellent wine and service.',
            'rating' => 4,
            'reviewed_at' => '2026-06-01',
            'source' => 'TripAdvisor',
            'is_active' => true,
            'show_on_dining' => false,
            'sort_order' => 2,
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('id="guest-reviews-title"', false)
            ->assertSee('What Our Guests Say')
            ->assertSee('Dining Guest')
            ->assertSee('<blockquote', false)
            ->assertSee('5 out of 5 stars')
            ->assertSee('Jul 2026')
            ->assertSee('Google')
            ->assertSee('guest-review-slider', false)
            ->assertDontSee('General Website Guest')
            ->assertDontSee('See More');
    }

    public function test_dining_reviews_do_not_fall_back_to_general_reviews(): void
    {
        GuestReview::query()->create([
            'reviewer_name' => 'General Restaurant Guest',
            'review_text' => 'Excellent restaurant food and wine. Beautiful dining experience.',
            'excerpt' => 'Excellent restaurant food and wine. Beautiful dining experience.',
            'rating' => 5,
            'is_active' => true,
            'show_on_dining' => false,
            'sort_order' => 1,
        ]);

        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertDontSee('General Restaurant Guest')
            ->assertDontSee('id="guest-reviews-title"', false);
    }

    public function test_all_active_dining_reviews_are_rendered(): void
    {
        foreach (range(1, 5) as $position) {
            GuestReview::query()->create([
                'reviewer_name' => "Dining Guest {$position}",
                'review_text' => "Dining review {$position}",
                'excerpt' => "Dining review {$position}",
                'rating' => 5,
                'is_active' => true,
                'show_on_dining' => true,
                'sort_order' => $position,
            ]);
        }

        $response = $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('data-total="5"', false);

        foreach (range(1, 5) as $position) {
            $response->assertSee("Dining Guest {$position}");
        }
    }

    public function test_practical_information_and_faq_are_rendered_after_testimonials(): void
    {
        $response = $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('Plan Your Visit')
            ->assertSee('You May Wonder')
            ->assertSee('Open to Outside Guests')
            ->assertDontSee('Dress Code')
            ->assertDontSee('Smart casual')
            ->assertSee('reservation@nandinibali.com')
            ->assertSee('Breakfast: 07:00 AM – 10:30 AM')
            ->assertSee('Lunch: 12:00 PM – 03:00 PM')
            ->assertSee('Dinner: 06:30 PM – 10:30 PM')
            ->assertSee('Do I need a reservation?')
            ->assertSee('aria-expanded', false)
            ->assertSee('https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing', false)
            ->assertSee('https://drive.google.com/file/d/16XoyEOdlRzFfN2Ca30THHhNfw8OCxWDN/view?usp=sharing', false)
            ->assertSee('https://drive.google.com/file/d/1Xm5YhSbX18muQQTdrLvNLYd7cgFw5EaS/view?usp=sharing', false);

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Plan Your Visit'), strpos($content, 'What Our Guests Say'));
    }

    public function test_private_dining_cta_opens_the_shared_inquiry_modal_with_dining_context(): void
    {
        $category = VoucherCategory::query()->create([
            'name' => 'Signature Dining Experiences',
            'slug' => 'signature-dining-experiences',
            'is_active' => true,
        ]);

        $expectedTitles = [
            'Romantic Dining by The Chapel',
            'Moonlit Jungle Romance',
            'Riverside Romance',
        ];

        foreach ($expectedTitles as $order => $title) {
            $slug = \Illuminate\Support\Str::slug($title);
            $experience = Experience::query()->updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'is_active' => true,
                'sort_order' => $order + 1,
            ]);
            Voucher::query()->create([
                'voucher_category_id' => $category->id,
                'experience_id' => $experience->id,
                'title' => $title,
                'slug' => 'inquiry-'.\Illuminate\Support\Str::slug($title),
                'sku' => 'INQUIRY-'.$order,
                'voucher_type' => 'dining',
                'selling_price' => 1,
                'is_active' => true,
                'sort_order' => $order + 1,
            ]);
        }

        Experience::query()->create([
            'title' => 'Unpublished Spa Choice',
            'slug' => 'unpublished-spa-choice',
            'is_active' => false,
            'sort_order' => 6,
        ]);

        $nonInquiryExperience = Experience::query()->updateOrCreate(['slug' => 'luxe-high-tea'], [
            'title' => 'Luxe High Tea',
            'is_active' => true,
            'sort_order' => 4,
        ]);
        Voucher::query()->create([
            'voucher_category_id' => $category->id,
            'experience_id' => $nonInquiryExperience->id,
            'title' => 'Luxe High Tea',
            'slug' => 'inquiry-non-private-dining',
            'sku' => 'INQUIRY-NON-PRIVATE',
            'voucher_type' => 'dining',
            'selling_price' => 1,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $response = $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('x-data="inquiryModal"', false)
            ->assertSee('data-inquiry-button', false)
            ->assertSee('data-inquiry-title="Private Dining — Dining / Special Occasions"', false)
            ->assertSee('action="/inquiries"', false)
            ->assertSee('name="source_url"', false)
            ->assertSee('name="inquiry_title"', false)
            ->assertSee('name="experience_id"', false)
            ->assertSee('name="occasion"', false)
            ->assertSee('Select experience')
            ->assertSee('Select occasion')
            ->assertSee('Honeymoon Dinner')
            ->assertSee('Anniversary')
            ->assertSee('Proposal')
            ->assertSee('Celebrations')
            ->assertDontSee('Unpublished Spa Choice')
            ->assertDontSee('>Luxe High Tea</option>', false);

        foreach ($expectedTitles as $title) {
            $response->assertSee($title);
        }

        $html = $response->getContent();
        $positions = array_map(fn(string $title): int|false => strpos($html, '>'.$title.'</option>'), $expectedTitles);
        $this->assertSame($positions, collect($positions)->sort()->values()->all());
    }

    public function test_dining_inquiry_requires_and_saves_a_published_experience(): void
    {
        Http::fake();
        config([
            'services.recaptcha.enabled' => false,
            'services.email_relay.url' => 'https://relay.example.test/send',
            'services.email_relay.token' => 'test-token',
        ]);

        $category = VoucherCategory::query()->create([
            'name' => 'Signature Dining Experiences',
            'slug' => 'signature-dining-experiences',
            'is_active' => true,
        ]);
        $experience = Experience::query()->updateOrCreate(['slug' => 'romantic-dining-by-the-chapel'], [
            'title' => 'Romantic Dining by The Chapel',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Voucher::query()->create([
            'voucher_category_id' => $category->id,
            'experience_id' => $experience->id,
            'title' => $experience->title,
            'slug' => 'chapel-inquiry-voucher-test',
            'sku' => 'CHAPEL-INQUIRY-TEST',
            'voucher_type' => 'dining',
            'selling_price' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $payload = [
            'title' => 'Mr',
            'first_name' => 'Test',
            'last_name' => 'Guest',
            'email' => 'guest@example.com',
            'country' => 'Indonesia',
            'phone_code' => '+62',
            'phone' => '8123456789',
            'inquiry_title' => 'Private Dining — Dining / Special Occasions',
            'reserve_date' => now()->addWeek()->toDateString(),
            'reserve_time' => '18:30',
            'source_url' => 'https://'.config('domains.dining').'/',
        ];

        $this->postJson('https://'.config('domains.dining').'/inquiries', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['experience_id'])
            ->assertJsonPath('errors.experience_id.0', 'Please select an experience.');

        $this->postJson('https://'.config('domains.dining').'/inquiries', [
            ...$payload,
            'experience_id' => $experience->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['occasion'])
            ->assertJsonPath('errors.occasion.0', 'Please select an occasion.');

        $this->postJson('https://'.config('domains.dining').'/inquiries', [
            ...$payload,
            'experience_id' => $experience->id,
            'occasion' => 'Proposal',
        ])->assertOk()->assertJsonPath('message', 'Thank you. Your inquiry has been sent.');

        $inquiry = Inquiry::query()->latest('id')->firstOrFail();
        $this->assertSame($experience->id, $inquiry->experience_id);
        $this->assertSame('Romantic Dining by The Chapel', $inquiry->experience->title);
        $this->assertSame('Proposal', $inquiry->occasion);
        Http::assertSent(fn($request): bool =>
            $request->url() === 'https://relay.example.test/send'
            && str_contains($request['subject'], 'Private Dining — Dining / Special Occasions — Romantic Dining by The Chapel')
            && str_contains($request['html_body'], 'Romantic Dining by The Chapel')
            && str_contains($request['html_body'], 'Proposal')
        );
    }

    public function test_dining_inquiry_submission_uses_the_existing_inquiry_controller(): void
    {
        $route = app('router')->getRoutes()->getByName('dining-landing.inquiries.store');

        $this->assertNotNull($route);
        $this->assertSame(config('domains.dining'), $route->getDomain());
        $this->assertSame('inquiries', $route->uri());
        $this->assertContains('POST', $route->methods());
        $this->assertSame(
            \App\Http\Controllers\InquiryController::class.'@store',
            $route->getActionName(),
        );
    }

    public function test_dining_experience_cards_link_to_matching_detail_pages(): void
    {
        $experiences = DiningExperience::query()->where('is_active', true)->get();

        $landingPage = $this->get('https://'.config('domains.dining').'/')
            ->assertOk();

        foreach ($experiences as $experience) {
            $url = route('dining-landing.experiences.show', ['experience' => $experience->slug]);

            $landingPage->assertSee($url, false);

            $this->get($url)
                ->assertOk()
                ->assertViewIs('pages.dining-landing.experience')
                ->assertSee($experience->card_title)
                ->assertSee($experience->short_description)
                ->assertSee(DiningSetting::query()->first()->experiences_heading)
                ->assertSee(DiningSetting::query()->first()->reservation_cta_url, false);
        }

        $this->get('https://'.config('domains.dining').'/experiences/not-a-dining-experience')
            ->assertNotFound();
    }

    public function test_dining_experiences_are_independent_from_general_experiences(): void
    {
        $diningExperience = DiningExperience::query()->orderBy('sort_order')->firstOrFail();
        $generalExperience = Experience::query()->find($diningExperience->getKey());

        $originalGeneralTitle = $generalExperience?->title;
        $diningExperience->update(['card_title' => 'Dining-only card title']);

        $this->assertSame('Dining-only card title', $diningExperience->fresh()->card_title);
        $this->assertSame($originalGeneralTitle, $generalExperience?->fresh()->title);
    }

    public function test_final_reservation_cta_uses_whatsapp_reservation_flow(): void
    {
        $response = $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('id="dining-reservation-cta-title"', false)
            ->assertSee('A Table Awaits in the Jungle')
            ->assertSee(DiningSetting::query()->first()->reservation_cta_url, false)
            ->assertDontSee('WhatsApp us')
            ->assertSee('/images/dining/romantic-dining-by-the-chapel.webp', false);

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'A Table Awaits in the Jungle'), strpos($content, 'Plan Your Visit'));
        $this->assertLessThan(strpos($content, 'Copyright'), strpos($content, 'A Table Awaits in the Jungle'));
    }

    public function test_remaining_landing_sections_use_ordered_cms_content_and_safe_line_breaks(): void
    {
        DiningSetting::query()->firstOrCreate()->update([
            'information_bar_items' => [
                ['icon' => 'email', 'label' => 'First bar item', 'value' => 'First value', 'link' => 'mailto:first@example.com'],
                ['icon' => 'clock', 'label' => 'Second bar item', 'value' => 'Second value'],
            ],
            'private_dining_eyebrow' => 'Private eyebrow',
            'private_dining_heading' => "Private\nheading",
            'private_dining_description' => 'Private description',
            'private_dining_cta_label' => 'Private action',
            'private_dining_cta_url' => 'https://example.com/private',
            'private_dining_tags' => [
                ['label' => 'First occasion', 'url' => 'https://example.com/first'],
                ['label' => 'Final occasion', 'url' => 'https://example.com/final'],
            ],
            'visit_eyebrow' => 'Visit eyebrow',
            'visit_heading' => 'Visit heading',
            'visit_information_items' => [
                ['icon' => 'location', 'label' => 'Visit location', 'value' => "First line\nSecond line"],
            ],
            'visit_food_menu_label' => 'Food action',
            'visit_food_menu_url' => 'https://example.com/food',
            'visit_beverage_menu_label' => 'Drink action',
            'visit_beverage_menu_url' => 'https://example.com/drinks',
            'faq_eyebrow' => 'FAQ eyebrow',
            'faq_heading' => 'FAQ heading',
            'faq_items' => [
                ['question' => 'A custom question?', 'answer' => '<script>alert(1)</script>'],
                ['question' => 'A second question?', 'answer' => 'A safe answer.'],
            ],
            'reservation_cta_heading' => 'Custom reservation heading',
            'reservation_cta_description' => "Reservation line one\nReservation line two",
            'reservation_cta_label' => 'Custom reserve action',
            'reservation_cta_url' => 'https://example.com/reserve',
        ]);

        $response = $this->get('https://'.config('domains.dining').'/')->assertOk()
            ->assertSee('mailto:first@example.com', false)
            ->assertSee('Private<br />', false)
            ->assertSee('https://example.com/private', false)
            ->assertSee('First line<br />', false)
            ->assertSee('Food action')->assertSee('Drink action')
            ->assertSee('A custom question?')->assertSee('A second question?')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Reservation line one<br />', false)
            ->assertSee('https://example.com/reserve', false);

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Second bar item'), strpos($content, 'First bar item'));
        $this->assertSame(2, substr_count($content, 'id="dining-faq-answer-'));
    }

    public function test_dining_hero_uses_a_four_by_three_mobile_ratio(): void
    {
        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('aspect-[4/3]', false)
            ->assertSee('md:aspect-auto', false)
            ->assertSee('hidden max-w-xl md:block', false)
            ->assertSee('w-[180%]', false)
            ->assertSee('bg-black/15 hidden md:block', false);
    }

    public function test_dining_card_images_keep_their_four_by_three_ratio_in_the_carousel(): void
    {
        $this->get('https://'.config('domains.dining').'/')
            ->assertOk()
            ->assertSee('dining-experience-image aspect-4/3 w-full shrink-0', false);
    }
}
