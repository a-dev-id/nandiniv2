<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\DiningSetting;
use App\Models\Experience;
use App\Rules\Recaptcha;
use App\Services\MembershipEmailRelayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $isDiningInquiry = $request->routeIs('dining-landing.inquiries.store');
        $occasionOptions = $isDiningInquiry
            ? DiningSetting::query()->find(1)?->occasionOptions() ?? DiningSetting::DEFAULT_OCCASIONS
            : [];

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:20'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'phone_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:2000'],
            'inquiry_title' => ['nullable', 'string', 'max:255'],
            'experience_id' => [
                $isDiningInquiry ? 'required' : 'nullable',
                'integer',
                Rule::exists('experiences', 'id')->where(function ($query): void {
                    $query->where('is_active', true)
                        ->whereIn('slug', Experience::DINING_INQUIRY_SLUGS)
                        ->whereExists(function ($query): void {
                        $query->selectRaw('1')
                            ->from('vouchers')
                            ->join('voucher_categories', 'voucher_categories.id', '=', 'vouchers.voucher_category_id')
                            ->whereColumn('vouchers.experience_id', 'experiences.id')
                            ->where('vouchers.is_active', true)
                            ->whereNull('vouchers.deleted_at')
                            ->where('voucher_categories.slug', 'signature-dining-experiences')
                            ->where('voucher_categories.is_active', true);
                    });
                }),
            ],
            'occasion' => [
                $isDiningInquiry ? 'required' : 'nullable',
                'string',
                'max:100',
                Rule::in($occasionOptions),
            ],
            'inquiry_image' => ['nullable', 'url', 'max:2048'],
            'reserve_date' => ['required', 'date'],
            'reserve_time' => ['required', 'date_format:H:i'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'g-recaptcha-response' => Recaptcha::rules(),
        ], [
            'experience_id.required' => 'Please select an experience.',
            'experience_id.exists' => 'Please select an available experience.',
            'occasion.required' => 'Please select an occasion.',
            'occasion.in' => 'Please select an available occasion.',
        ]);
        unset($validated['g-recaptcha-response']);

        $name = trim($validated['title'] . ' ' . $validated['first_name'] . ' ' . $validated['last_name']);
        $sourceUrl = $validated['source_url'] ?? url()->previous();
        $validated['inquiry_title'] = $validated['inquiry_title'] ?: 'Nandini Inquiry';

        if ($this->requiresLateStart($validated['inquiry_title']) && $validated['reserve_time'] < '16:00') {
            throw ValidationException::withMessages([
                'reserve_time' => 'Dinner and night activities start after 16:00.',
            ]);
        }

        $inquiry = Inquiry::create([
            ...$validated,
            'source_url' => $sourceUrl,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'submitted_at' => now(),
        ]);
        $inquiry->load('experience');

        $this->sendNotification($inquiry, $validated, $name, $sourceUrl);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Thank you. Your inquiry has been sent.',
            ]);
        }

        return back()->with('inquiry_status', 'Thank you. Your inquiry has been sent.');
    }

    /**
     * @param array<string, string|null> $data
     */
    private function sendNotification(Inquiry $inquiry, array $data, string $name, string $sourceUrl): void
    {
        $recipient = config('mail.inquiry_recipient', 'reservation@nandinibali.com');

        try {
            $result = app(MembershipEmailRelayService::class)->sendView('emails.inquiry.guest', [
                'inquiry' => $inquiry,
                'guestName' => $name,
                'sourceUrl' => $sourceUrl,
                'requiresLateStart' => $this->requiresLateStart((string) $inquiry->inquiry_title),
            ], [
                'to' => $data['email'],
                'cc' => [$recipient],
                'bcc' => $this->guestBcc(),
                'subject' => 'Your Inquiry: '.$inquiry->inquiry_title
                    .($inquiry->experience ? ' — '.$inquiry->experience->title : ''),
                'reply_to' => config('mail.guest_reply_to'),
            ]);

            if (! $result['success']) {
                $inquiry->update([
                    'email_error' => $result['error'] ?? 'Email relay failed.',
                ]);

                Log::warning('Inquiry email could not be sent through relay.', [
                    'inquiry_id' => $inquiry->id,
                    'relay_response' => $result,
                ]);

                return;
            }

            $inquiry->update([
                'email_sent_at' => now(),
                'email_error' => null,
            ]);
        } catch (\Throwable $exception) {
            $inquiry->update([
                'email_error' => $exception->getMessage(),
            ]);

            Log::warning('Inquiry email could not be sent.', [
                'inquiry_id' => $inquiry->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : [$bcc];
    }

    private function requiresLateStart(string $title): bool
    {
        $title = strtolower($title);

        return str_contains($title, 'dinner') || str_contains($title, 'night');
    }
}
