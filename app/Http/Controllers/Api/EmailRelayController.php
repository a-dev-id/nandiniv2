<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailRelayController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $configuredToken = trim((string) config('services.email_relay.token'));
        $providedToken = trim((string) $request->bearerToken());

        abort_unless(
            $configuredToken !== '' && $providedToken !== '' && hash_equals($configuredToken, $providedToken),
            403
        );

        $validator = Validator::make($request->all(), [
            'to' => ['required', 'email:rfc'],
            'cc' => ['sometimes', 'array'],
            'cc.*' => ['email:rfc'],
            'bcc' => ['sometimes', 'array'],
            'bcc.*' => ['email:rfc'],
            'subject' => ['required', 'string', 'max:255'],
            'html_body' => ['required', 'string'],
            'text_body' => ['nullable', 'string'],
            'reply_to' => ['nullable', 'email:rfc'],
            'attachments' => ['sometimes', 'array', 'max:10'],
            'attachments.*.filename' => ['required', 'string', 'max:255'],
            'attachments.*.content_type' => ['required', 'string', 'max:127'],
            'attachments.*.content_base64' => ['required', 'string'],
        ]);

        $data = $validator->validate();
        $attachments = collect($data['attachments'] ?? [])->map(function (array $attachment): array {
            $content = base64_decode($attachment['content_base64'], true);

            abort_if($content === false, 422, 'An attachment is not valid Base64 data.');
            abort_if(strlen($content) > 10 * 1024 * 1024, 422, 'An attachment exceeds the 10 MB limit.');

            return [
                'filename' => basename($attachment['filename']),
                'content_type' => $attachment['content_type'],
                'content' => $content,
            ];
        })->all();

        Mail::html($data['html_body'], function (Message $message) use ($data, $attachments): void {
            $message->to($data['to'])->subject($data['subject']);

            if (! empty($data['cc'])) {
                $message->cc($data['cc']);
            }

            if (! empty($data['bcc'])) {
                $message->bcc($data['bcc']);
            }

            if (! empty($data['reply_to'])) {
                $message->replyTo($data['reply_to']);
            }

            foreach ($attachments as $attachment) {
                $message->attachData($attachment['content'], $attachment['filename'], [
                    'mime' => $attachment['content_type'],
                ]);
            }
        });

        return response()->json([
            'ok' => true,
            'attachments' => count($attachments),
        ]);
    }
}
