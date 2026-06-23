<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectDisabledMembership
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (config('features.disable_membership_feature')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
