<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SeparateMemberAndAffiliateSessions
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('member')->check() && Auth::guard('affiliate')->check()) {
            $middleware = $request->route()?->gatherMiddleware() ?? [];

            if (in_array('affiliate.auth', $middleware, true)) {
                Auth::guard('member')->logout();
            } else {
                Auth::guard('affiliate')->logout();
                $request->session()->forget('affiliate.pending-review-notice-shown');
            }
        }

        return $next($request);
    }
}
