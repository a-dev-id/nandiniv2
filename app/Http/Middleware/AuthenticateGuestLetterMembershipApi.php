<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateGuestLetterMembershipApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('services.guestletter_membership_api.token', '');
        $bearerToken = (string) $request->bearerToken();

        if (trim($configuredToken) === '' || $bearerToken === '' || ! hash_equals($configuredToken, $bearerToken)) {
            return new JsonResponse([
                'ok' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        return $next($request);
    }
}
