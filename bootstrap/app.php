<?php

use App\Http\Middleware\EnsureAffiliateAuthenticated;
use App\Http\Middleware\EnsureAffiliateRegistrationEnabled;
use App\Http\Middleware\EnsureAffiliateRegistrationGuest;
use App\Http\Middleware\RedirectDisabledAffiliate;
use App\Http\Middleware\RedirectDisabledMembership;
use App\Http\Middleware\RedirectDisabledVoucher;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/short-link.php',
            __DIR__.'/../routes/voucher.php',
            __DIR__.'/../routes/affiliate.php',
            __DIR__.'/../routes/web.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->alias([
            'affiliate.auth' => EnsureAffiliateAuthenticated::class,
            'affiliate.registration.enabled' => EnsureAffiliateRegistrationEnabled::class,
            'affiliate.enabled' => RedirectDisabledAffiliate::class,
            'affiliate.registration.guest' => EnsureAffiliateRegistrationGuest::class,
            'membership.enabled' => RedirectDisabledMembership::class,
            'voucher.enabled' => RedirectDisabledVoucher::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            $routeName = $request->route()?->getName();

            if (! is_string($routeName) || ! str_starts_with($routeName, 'affiliate.') || ! in_array($exception->getStatusCode(), [403, 404], true)) {
                return null;
            }

            $forbidden = $exception->getStatusCode() === 403;

            return response()->view('pages.affiliate.portal-unavailable', [
                'title' => $forbidden ? 'This page is unavailable for this account' : 'The requested resource is unavailable',
                'message' => $forbidden
                    ? 'Active Affiliate tools are available only to approved accounts with the required access.'
                    : 'The requested Affiliate resource is unavailable or is no longer active.',
            ], $exception->getStatusCode());
        });
    })->create();
