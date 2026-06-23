<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NandiniMembershipApiService
{
    public function login(array $credentials): array
    {
        $url = $this->urlFor((string) config('services.nandini_membership.login_endpoint'));

        if ($url === '') {
            return [
                'success' => false,
                'configured' => false,
                'message' => 'Membership login API is not configured.',
                'member' => [],
            ];
        }

        try {
            $response = $this->request()->post($url, [
                'email' => $credentials['email'] ?? null,
                'password' => $credentials['password'] ?? null,
                'remember' => (bool) ($credentials['remember'] ?? false),
            ]);

            $payload = $response->json() ?? [];

            if (! $response->successful()) {
                Log::warning('Nandini membership API login request failed.', [
                    'email' => $credentials['email'] ?? null,
                    'url' => $url,
                    'status' => $response->status(),
                    'message' => $this->rawMessageFromPayload($payload),
                ]);

                return [
                    'success' => false,
                    'configured' => true,
                    'message' => $this->publicMessageFromPayload($payload, 'The email or password is incorrect.'),
                    'member' => [],
                ];
            }

            $login = $this->normalizeLogin($payload);

            if (! $login['success']) {
                return [
                    ...$login,
                    'configured' => true,
                ];
            }

            return [
                ...$login,
                'configured' => true,
            ];
        } catch (\Throwable $e) {
            Log::warning('Nandini membership API login request error.', [
                'email' => $credentials['email'] ?? null,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'configured' => true,
                'message' => 'Unable to connect to the membership login service. Please try again.',
                'member' => [],
            ];
        }
    }

    public function fetchMemberByEmail(string $email): array
    {
        $url = $this->urlFor((string) config('services.nandini_membership.member_endpoint'));
        $email = strtolower(trim($email));

        if ($url === '' || $email === '') {
            return $this->emptyDashboard();
        }

        try {
            $response = $this->request()->get($url, [
                'email' => $email,
            ]);

            if (! $response->successful()) {
                Log::warning('Nandini membership API member request failed.', [
                    'email' => $email,
                    'status' => $response->status(),
                ]);

                return $this->emptyDashboard();
            }

            return $this->normalizeDashboard($response->json() ?? []);
        } catch (\Throwable $e) {
            Log::warning('Nandini membership API member request error.', [
                'email' => $email,
                'message' => $e->getMessage(),
            ]);

            return $this->emptyDashboard();
        }
    }

    public function changePassword(string $email, string $currentPassword, string $password, string $passwordConfirmation, bool $remember = false): array
    {
        $url = $this->urlFor((string) config('services.nandini_membership.change_password_endpoint'));
        $email = strtolower(trim($email));

        if ($url === '' || $email === '') {
            return [
                'success' => false,
                'configured' => false,
                'message' => 'Membership change-password API is not configured.',
                'member' => [],
            ];
        }

        try {
            $response = $this->request()->post($url, [
                'email' => $email,
                'current_password' => $currentPassword,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
                'remember' => $remember,
            ]);

            $payload = $response->json() ?? [];

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'configured' => true,
                    'message' => $this->publicMessageFromPayload($payload, 'Unable to update your password. Please try again.'),
                    'member' => [],
                ];
            }

            return [
                ...$this->normalizeLogin($payload),
                'success' => Arr::get($payload, 'changed', true) !== false,
                'configured' => true,
            ];
        } catch (\Throwable $e) {
            Log::warning('Nandini membership API change-password request error.', [
                'email' => $email,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'configured' => true,
                'message' => 'Unable to connect to the membership change-password service. Please try again.',
                'member' => [],
            ];
        }
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        $request = Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.nandini_membership.timeout', 8));

        $token = config('services.nandini_membership.token');

        if (filled($token)) {
            $request = $request->withToken((string) $token);
        }

        return $request;
    }

    private function urlFor(string $endpoint): string
    {
        $endpoint = trim($endpoint);

        if ($endpoint === '') {
            return '';
        }

        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

        $baseUrl = rtrim((string) config('services.nandini_membership.base_url'), '/');

        if ($baseUrl === '') {
            return '';
        }

        return $baseUrl . '/' . ltrim($endpoint, '/');
    }

    private function normalizeLogin(array $payload): array
    {
        if (Arr::get($payload, 'success') === false || Arr::get($payload, 'status') === false) {
            return [
                'success' => false,
                'message' => $this->publicMessageFromPayload($payload, 'The email or password is incorrect.'),
                'member' => [],
                'token' => null,
            ];
        }

        $data = Arr::get($payload, 'data', $payload);
        $member = $this->firstArray($data, [
            'member',
            'profile',
            'user',
            'customer',
            'membership.member',
            'membership.profile',
        ]);

        if ($member === []) {
            $member = $this->looksLikeMember($data) ? $data : [];
        }

        return [
            'success' => $member !== [] || filled($this->tokenFromPayload($payload)),
            'message' => $this->publicMessageFromPayload($payload, null),
            'member' => $member,
            'token' => $this->tokenFromPayload($payload),
            'redirect_url' => $this->redirectUrlFromPayload($payload),
            'must_change_password' => (bool) Arr::get($payload, 'must_change_password', Arr::get($member, 'must_change_password', false)),
            'next_action' => Arr::get($payload, 'next_action'),
        ];
    }

    private function publicMessageFromPayload(array $payload, ?string $fallback): ?string
    {
        $message = $this->rawMessageFromPayload($payload);

        if ($message === null) {
            return $fallback;
        }

        if ($this->looksLikeInfrastructureError($message)) {
            return 'The membership service is temporarily unavailable. Please try again later.';
        }

        return $message;
    }

    private function rawMessageFromPayload(array $payload): ?string
    {
        foreach (['message', 'error', 'errors.email.0', 'data.message'] as $key) {
            $message = Arr::get($payload, $key);

            if (is_string($message) && trim($message) !== '') {
                return $message;
            }
        }

        return null;
    }

    private function looksLikeInfrastructureError(string $message): bool
    {
        return str_contains($message, 'SQLSTATE')
            || str_contains($message, 'QueryException')
            || str_contains($message, 'Base table or view not found')
            || str_contains($message, 'Connection:')
            || str_contains($message, 'select * from')
            || str_contains($message, 'insert into')
            || str_contains($message, 'update `')
            || str_contains($message, 'delete from');
    }

    private function tokenFromPayload(array $payload): ?string
    {
        foreach (['token', 'access_token', 'data.token', 'data.access_token', 'data.authorization.token'] as $key) {
            $token = Arr::get($payload, $key);

            if (is_string($token) && trim($token) !== '') {
                return $token;
            }
        }

        return null;
    }

    private function redirectUrlFromPayload(array $payload): ?string
    {
        foreach (['redirect_url', 'data.redirect_url', 'url', 'data.url'] as $key) {
            $url = Arr::get($payload, $key);

            if (is_string($url) && trim($url) !== '') {
                return trim($url);
            }
        }

        return null;
    }

    private function normalizeDashboard(array $payload): array
    {
        $data = Arr::get($payload, 'data', $payload);
        $member = $this->firstArray($data, [
            'member',
            'profile',
            'user',
            'customer',
            'membership.member',
            'membership.profile',
        ]);

        if ($member === []) {
            $member = $this->looksLikeMember($data) ? $data : [];
        }

        return [
            'member' => $member,
            'histories' => collect([
                ...$this->arrayAt($data, 'histories'),
                ...$this->arrayAt($data, 'history'),
                ...$this->arrayAt($data, 'activities'),
                ...$this->arrayAt($data, 'activity_histories'),
                ...$this->arrayAt($data, 'point_histories'),
                ...$this->arrayAt($data, 'point_transactions'),
                ...$this->arrayAt($data, 'transactions'),
                ...$this->arrayAt($data, 'reward_histories'),
                ...$this->arrayAt($data, 'reward_redemptions'),
                ...$this->arrayAt($data, 'redemptions'),
                ...$this->arrayAt($data, 'membership.histories'),
                ...$this->arrayAt($data, 'membership.transactions'),
            ])
                ->filter(fn ($item) => is_array($item) || is_object($item))
                ->values()
                ->all(),
        ];
    }

    private function firstArray(array $payload, array $keys): array
    {
        foreach ($keys as $key) {
            $value = Arr::get($payload, $key);

            if (is_array($value) && Arr::isAssoc($value)) {
                return $value;
            }
        }

        return [];
    }

    private function arrayAt(array $payload, string $key): array
    {
        $value = Arr::get($payload, $key, []);

        if (! is_array($value)) {
            return [];
        }

        if (Arr::isAssoc($value)) {
            $nestedData = Arr::get($value, 'data');

            if (is_array($nestedData)) {
                return Arr::isAssoc($nestedData) ? [$nestedData] : $nestedData;
            }

            if ($this->looksLikeHistory($value)) {
                return [$value];
            }

            return array_values($value);
        }

        return $value;
    }

    private function looksLikeMember(array $payload): bool
    {
        return collect(['email', 'name', 'first_name', 'last_name', 'points', 'tier', 'membership'])
            ->contains(fn (string $key) => Arr::has($payload, $key));
    }

    private function looksLikeHistory(array $payload): bool
    {
        return collect(['title', 'reward_name', 'description', 'status', 'type', 'points', 'points_used', 'created_at'])
            ->contains(fn (string $key) => Arr::has($payload, $key));
    }

    private function emptyDashboard(): array
    {
        return [
            'member' => [],
            'histories' => [],
        ];
    }
}
