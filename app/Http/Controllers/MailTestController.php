<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function __invoke(string $token, Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.mail_test_token');

        if ($expectedToken === '') {
            return $this->invalidTokenResponse();
        }

        if (! hash_equals($expectedToken, $token)) {
            return $this->invalidTokenResponse();
        }

        $email = trim((string) $request->query('email'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'A valid email address is required.',
            ], 422);
        }

        try {
            Mail::raw(
                'This is a test email from Nandini Jungle by Hanging Gardens. If you received this email, the mail configuration is working.',
                function ($message) use ($email) {
                    $bcc = trim((string) config('mail.guest_bcc'));

                    $message->to($email)
                        ->subject('Nandini Mail Test');

                    if ($bcc !== '') {
                        $message->bcc($bcc);
                    }
                }
            );
        } catch (\Throwable $e) {
            Log::error('Mail test failed.', [
                'email' => $email,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test email could not be sent.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully.',
        ]);
    }

    private function invalidTokenResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Invalid mail test token.',
        ], 403);
    }
}
