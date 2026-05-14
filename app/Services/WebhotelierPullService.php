<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WebhotelierPullService
{
    protected string $baseUrl;
    protected ?string $username;
    protected ?string $password;
    protected ?string $propertyCode;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.webhotelier.api_base_url'), '/');
        $this->username = config('services.webhotelier.api_username');
        $this->password = config('services.webhotelier.api_password');
        $this->propertyCode = config('services.webhotelier.property_code');
    }

    public function isConfigured(): bool
    {
        return filled($this->baseUrl)
            && filled($this->username)
            && filled($this->password)
            && filled($this->propertyCode);
    }

    public function configStatus(): array
    {
        return [
            'base_url' => $this->baseUrl,
            'username_set' => filled($this->username),
            'password_set' => filled($this->password),
            'property_code' => $this->propertyCode,
            'is_configured' => $this->isConfigured(),
        ];
    }

    public function propertyCode(): string
    {
        $this->ensureConfigured();

        return $this->propertyCode;
    }

    public function get(string $endpoint, array $query = []): array
    {
        $this->ensureConfigured();

        $response = $this->request()
            ->get($this->url($endpoint), $query);

        return $this->handleResponse($response);
    }

    public function post(string $endpoint, array $payload = []): array
    {
        $this->ensureConfigured();

        $response = $this->request()
            ->post($this->url($endpoint), $payload);

        return $this->handleResponse($response);
    }

    public function getPendingReservations(): array
    {
        return $this->get('/reservation/new');
    }

    public function getReservation(int|string $reservationId): array
    {
        return $this->get('/reservation/' . $reservationId);
    }

    public function markReservationAsSynced(int|string $reservationId): array
    {
        return $this->post('/reservation/sync/' . $reservationId);
    }

    protected function request()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(2, 1000);
    }

    protected function url(string $endpoint): string
    {
        return $this->baseUrl . '/' . ltrim($endpoint, '/');
    }

    protected function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        throw new RuntimeException(
            'WebHotelier API request failed. Status: '
                . $response->status()
                . '. Body: '
                . $response->body()
        );
    }

    protected function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('WebHotelier API is not fully configured. Please check .env and config/services.php.');
        }
    }
}
