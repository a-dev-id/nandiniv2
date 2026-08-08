<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliateAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('affiliate')->check()) {
            return redirect()->guest(route('affiliate.login'));
        }

        $affiliate = Auth::guard('affiliate')->user();

        abort_unless(
            $affiliate?->hasPermissionTo(Permission::AFFILIATE_DASHBOARD_VIEW_OWN),
            403
        );

        return $next($request);
    }
}
