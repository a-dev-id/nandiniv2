<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectDisabledVoucher
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (config('features.disable_voucher_feature')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(404);
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
