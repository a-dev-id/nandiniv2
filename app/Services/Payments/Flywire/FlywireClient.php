<?php

namespace App\Services\Payments\Flywire;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FlywireClient
{
    public function post(string $path, array $payload): array
    {
        return $this->request('post', $path, $payload);
    }

    public function get(string $path): array
    {
        return $this->request('get', $path);
    }

    private function request(string $method, string $path, array $payload = []): array
    {
        $baseUrl = rtrim((string) config('services.flywire.base_url'), '/');
        $apiKey = (string) config('services.flywire.api_key');

        if ($baseUrl === '' || $apiKey === '') {
            throw new RuntimeException('Flywire is not configured.');
        }

        try {
            $request = Http::baseUrl($baseUrl)
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->withHeaders(['X-Authentication-Key' => $apiKey]);

            $response = $method === 'get'
                ? $request->retry(2, 200)->get($path)
                : $request->post($path, $payload);

            $response->throw();

            $json = $response->json();

            if (! is_array($json)) {
                throw new RuntimeException('Flywire returned malformed JSON.');
            }

            return $json;
        } catch (ConnectionException|RequestException $e) {
            report($e);
            throw new RuntimeException('Flywire request failed. Please try again.', previous: $e);
        }
    }
}
