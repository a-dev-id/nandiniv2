<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WebhotelierPullService
{
    protected string $baseUrl;

    protected ?string $username;

    protected ?string $password;

    protected ?string $propertyCode;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.webhotelier.api_base_url'), '/');
        $this->username = config('services.webhotelier.api_username');
        $this->password = config('services.webhotelier.api_password');
        $this->propertyCode = config('services.webhotelier.property_code');
        $this->timeout = (int) config('services.webhotelier.timeout', 30);
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
            'timeout' => $this->timeout,
            'is_configured' => $this->isConfigured(),
        ];
    }

    public function propertyCode(): string
    {
        $this->ensureConfigured();

        return (string) $this->propertyCode;
    }

    public function get(string $endpoint, array $query = []): array
    {
        $this->ensureConfigured();

        $response = $this->request()
            ->get($this->url($endpoint), $query);

        return $this->handleResponse($response);
    }

    /*
    |--------------------------------------------------------------------------
    | WebHotelier PULL Flow
    |--------------------------------------------------------------------------
    | 1. List pending bookings: /reservation/new
    | 2. Retrieve booking:      /reservation/{res_id}
    | 3. Mark as synced:       /reservation/sync/{res_id}
    */

    public function listPendingBookings(): array
    {
        return $this->get('/reservation/new');
    }

    public function getPendingReservations(): array
    {
        return $this->listPendingBookings();
    }

    public function retrieveBooking(int|string $reservationId): array
    {
        $reservationId = $this->cleanReservationId($reservationId);

        return $this->get('/reservation/' . rawurlencode($reservationId));
    }

    public function getReservation(int|string $reservationId): array
    {
        return $this->retrieveBooking($reservationId);
    }

    public function markBookingAsSynced(int|string $reservationId): array
    {
        $reservationId = $this->cleanReservationId($reservationId);

        return $this->get('/reservation/sync/' . rawurlencode($reservationId));
    }

    public function markReservationAsSynced(int|string $reservationId): array
    {
        return $this->markBookingAsSynced($reservationId);
    }

    protected function request(): PendingRequest
    {
        return Http::withBasicAuth((string) $this->username, (string) $this->password)
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout)
            ->connectTimeout(10)
            ->retry(2, 1000, null, false);
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

        $body = $response->json();

        if (! is_array($body)) {
            $body = [
                'raw_body' => $response->body(),
            ];
        }

        throw new RuntimeException(
            'WebHotelier API request failed. Status: '
                . $response->status()
                . '. Body: '
                . json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    protected function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('WebHotelier API is not fully configured. Please check .env and config/services.php.');
        }
    }

    protected function cleanReservationId(int|string $reservationId): string
    {
        $reservationId = trim((string) $reservationId);

        if ($reservationId === '') {
            throw new RuntimeException('WebHotelier reservation ID is empty.');
        }

        return $reservationId;
    }
}
