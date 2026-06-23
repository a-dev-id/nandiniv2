<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
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
            'inquiry_image' => ['nullable', 'url', 'max:2048'],
            'reserve_date' => ['required', 'date'],
            'reserve_time' => ['required', 'date_format:H:i'],
            'source_url' => ['nullable', 'url', 'max:2048'],
        ]);

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
            Mail::send('emails.inquiry.guest', [
                'inquiry' => $inquiry,
                'guestName' => $name,
                'sourceUrl' => $sourceUrl,
                'requiresLateStart' => $this->requiresLateStart((string) $inquiry->inquiry_title),
            ], function ($message) use ($recipient, $data, $name, $inquiry) {
                $bcc = trim((string) config('mail.guest_bcc'));

                $message
                    ->to($data['email'], $name)
                    ->cc($recipient)
                    ->replyTo($recipient, 'Nandini Reservations')
                    ->subject('Your Inquiry: ' . $inquiry->inquiry_title);

                if ($bcc !== '') {
                    $message->bcc($bcc);
                }
            });

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

    private function requiresLateStart(string $title): bool
    {
        $title = strtolower($title);

        return str_contains($title, 'dinner') || str_contains($title, 'night');
    }
}
