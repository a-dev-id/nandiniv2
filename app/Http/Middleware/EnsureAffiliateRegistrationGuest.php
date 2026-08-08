<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliateRegistrationGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('affiliate')->check()) {
            return redirect()->route('affiliate.dashboard');
        }

        return $next($request);
    }
}
