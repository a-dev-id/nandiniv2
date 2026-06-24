<?php

namespace App\Http\Controllers;

use App\Services\MembershipEmailRelayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $body = 'This is a test email from Nandini Jungle by Hanging Gardens. If you received this email, the email relay configuration is working.';

            // The mail test now verifies the relay API instead of local SMTP.
            $result = app(MembershipEmailRelayService::class)->send([
                'to' => $email,
                'bcc' => $this->guestBcc(),
                'subject' => 'Nandini Mail Test',
                'html_body' => nl2br(e($body)),
                'text_body' => $body,
            ]);

            if (! $result['success']) {
                Log::error('Mail relay test failed.', [
                    'email' => $email,
                    'relay_response' => $result,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Test email could not be sent.',
                ], 500);
            }
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

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : [$bcc];
    }
}
