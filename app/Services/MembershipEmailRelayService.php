<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MembershipEmailRelayService
{
    /**
     * Send email through membership.nandiniapps.cloud because this website host
     * is not responsible for direct SMTP delivery anymore.
     *
     * @param array<string, mixed> $payload
     * @return array{success: bool, status: int|null, response: mixed, error: string|null}
     */
    public function send(array $payload): array
    {
        $url = trim((string) config('services.email_relay.url'));
        $token = trim((string) config('services.email_relay.token'));

        $payload = $this->normalizePayload($payload);
        $logContext = [
            'recipient' => $payload['to'],
            'subject' => $payload['subject'],
        ];

        if ($url === '' || $token === '') {
            $result = [
                'success' => false,
                'status' => null,
                'response' => null,
                'error' => 'Email relay URL or token is not configured.',
            ];

            Log::warning('Email relay request failed.', $logContext + $result);

            return $result;
        }

        try {
            $response = Http::timeout(20)
                ->withToken($token)
                ->post($url, $payload);

            $responseBody = $this->responseBody($response);

            if ($response->successful()) {
                Log::info('Email relay request succeeded.', $logContext + [
                    'status' => $response->status(),
                ]);

                return [
                    'success' => true,
                    'status' => $response->status(),
                    'response' => $responseBody,
                    'error' => null,
                ];
            }

            $result = [
                'success' => false,
                'status' => $response->status(),
                'response' => $responseBody,
                'error' => 'Email relay returned an unsuccessful response.',
            ];

            Log::warning('Email relay request failed.', $logContext + $result);

            return $result;
        } catch (\Throwable $exception) {
            $result = [
                'success' => false,
                'status' => null,
                'response' => null,
                'error' => $exception->getMessage(),
            ];

            Log::warning('Email relay request failed.', $logContext + $result);

            return $result;
        }
    }

    /**
     * Render an existing Blade email template and relay the HTML.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $payload
     * @return array{success: bool, status: int|null, response: mixed, error: string|null}
     */
    public function sendView(string $view, array $data, array $payload): array
    {
        $html = view($view, $data)->render();

        return $this->send($payload + [
            'html_body' => $html,
            'text_body' => $this->htmlToText($html),
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload): array
    {
        $html = (string) ($payload['html_body'] ?? '');

        return [
            'to' => $payload['to'] ?? null,
            'cc' => $this->mergeRecipients($payload['cc'] ?? []),
            'bcc' => $this->mergeRecipients($payload['bcc'] ?? []),
            'subject' => (string) ($payload['subject'] ?? ''),
            'html_body' => $html,
            'text_body' => (string) ($payload['text_body'] ?? $this->htmlToText($html)),
            'reply_to' => $payload['reply_to'] ?? config('mail.guest_reply_to'),
            'source' => parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'nandinibali.com',
        ];
    }

    private function htmlToText(string $html): string
    {
        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /**
     * @return array<int, string>
     */
    private function mergeRecipients(mixed ...$values): array
    {
        return collect($values)
            ->flatMap(function (mixed $value): array {
                if (is_array($value)) {
                    return $value;
                }

                return [$value];
            })
            ->flatMap(fn(mixed $value): array => explode(',', (string) $value))
            ->map(fn(mixed $value): string => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function responseBody(object $response): mixed
    {
        try {
            return $response->json();
        } catch (\Throwable) {
            return $response->body();
        }
    }
}
