<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MembershipBookingApiService
{
    private ?string $lastUrlCalled = null;

    private bool $lastSuccess = false;

    private int $lastBookingsCount = 0;

    private ?string $lastMessage = null;

    public function fetchBookings(?string $since): array
    {
        $this->ensureConfigured();

        $url = rtrim((string) config('services.membership_api.url'), '/') . '/api/bookings/sync';
        $query = array_filter(['since' => $since], fn ($value) => $value !== null && $value !== '');

        $this->lastUrlCalled = $url . ($query === [] ? '' : '?' . Arr::query($query));
        $this->lastSuccess = false;
        $this->lastBookingsCount = 0;
        $this->lastMessage = null;

        try {
            $response = Http::acceptJson()
                ->withToken((string) config('services.membership_api.token'))
                ->timeout((int) config('services.membership_api.timeout', 20))
                ->get($url, $query);
        } catch (ConnectionException $e) {
            $this->lastMessage = 'Unable to connect to the booking sync API.';

            Log::warning('Membership booking API connection failed.', [
                'message' => $e->getMessage(),
            ]);

            throw new RuntimeException('Unable to connect to the booking sync API.');
        }

        $payload = $response->json() ?? [];
        $this->lastMessage = $payload['message'] ?? null;

        if ($response->unauthorized()) {
            $this->lastMessage = 'Booking sync API authorization failed.';

            Log::warning('Membership booking API unauthorized response.');
            throw new RuntimeException('Booking sync API authorization failed.');
        }

        if ($response->status() === 422) {
            $this->lastMessage = $payload['message'] ?? 'Booking sync API rejected the sync request.';

            Log::warning('Membership booking API validation response.', [
                'message' => $payload['message'] ?? null,
            ]);

            throw new RuntimeException('Booking sync API rejected the sync request.');
        }

        if ($response->serverError()) {
            $this->lastMessage = 'Booking sync API is temporarily unavailable.';

            Log::warning('Membership booking API server error.', [
                'status' => $response->status(),
            ]);

            throw new RuntimeException('Booking sync API is temporarily unavailable.');
        }

        if (! $response->successful() || ($payload['success'] ?? false) !== true) {
            $this->lastMessage = $payload['message'] ?? 'Booking sync API returned an unsuccessful response.';

            Log::warning('Membership booking API returned an unsuccessful response.', [
                'status' => $response->status(),
                'message' => $payload['message'] ?? null,
            ]);

            throw new RuntimeException('Booking sync API returned an unsuccessful response.');
        }

        $bookings = $payload['bookings'] ?? [];

        if (! is_array($bookings)) {
            $this->lastMessage = 'Booking sync API returned an invalid bookings payload.';

            throw new RuntimeException('Booking sync API returned an invalid bookings payload.');
        }

        $this->lastSuccess = true;
        $this->lastBookingsCount = count($bookings);
        $this->lastMessage = $payload['message'] ?? 'Booking sync API returned bookings.';

        return $bookings;
    }

    public function debugData(): array
    {
        return [
            'membership_api_url_called' => $this->lastUrlCalled,
            'membership_api_success' => $this->lastSuccess,
            'membership_api_bookings_count' => $this->lastBookingsCount,
            'membership_api_message' => $this->lastMessage,
        ];
    }

    private function ensureConfigured(): void
    {
        if (! filled(config('services.membership_api.url')) || ! filled(config('services.membership_api.token'))) {
            throw new RuntimeException('Membership booking API is not configured.');
        }
    }
}
