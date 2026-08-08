<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliateRegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('features.affiliate_registration_enabled')) {
            return response()->view('pages.affiliate.auth.registration-unavailable', status: 503);
        }

        return $next($request);
    }
}
