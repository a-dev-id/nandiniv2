<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AffiliateAuthController extends Controller
{
    public function create(): View
    {
        return view('pages.affiliate.auth.sign-in');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('affiliate')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The email or password is incorrect.',
            ]);
        }

        $affiliate = Auth::guard('affiliate')->user();

        if (! $affiliate?->hasPermissionTo(Permission::AFFILIATE_DASHBOARD_VIEW_OWN)) {
            Auth::guard('affiliate')->logout();

            throw ValidationException::withMessages([
                'email' => 'This account does not have access to the affiliate portal.',
            ]);
        }

        $affiliate->forceFill(['last_login_at' => now()])->save();

        Auth::guard('member')->logout();
        $request->session()->regenerate();
        $request->session()->forget('affiliate.pending-review-notice-shown');
        $request->session()->forget('affiliate.approved-welcome-notice-shown');
        $request->session()->forget('url.intended');

        return redirect()->route('affiliate.dashboard');
    }

    public function redirectToSocialProvider(string $provider): RedirectResponse
    {
        abort_unless($this->socialProviderIsSupported($provider), 404);

        if (! $this->socialProviderIsConfigured($provider)) {
            return redirect()
                ->route('affiliate.login')
                ->withErrors(['email' => ucfirst($provider).' sign in is not configured yet.']);
        }

        return Socialite::driver($provider)
            ->redirectUrl($this->socialRedirectUrl($provider))
            ->redirect();
    }

    public function handleSocialProviderCallback(Request $request, string $provider): RedirectResponse
    {
        abort_unless($this->socialProviderIsSupported($provider), 404);

        if (! $this->socialProviderIsConfigured($provider)) {
            return redirect()
                ->route('affiliate.login')
                ->withErrors(['email' => ucfirst($provider).' sign in is not configured yet.']);
        }

        try {
            $socialUser = Socialite::driver($provider)
                ->redirectUrl($this->socialRedirectUrl($provider))
                ->user();
        } catch (Throwable) {
            return redirect()
                ->route('affiliate.login')
                ->withErrors(['email' => 'Unable to sign in with '.ucfirst($provider).'. Please try again.']);
        }

        $email = strtolower((string) $socialUser->getEmail());

        if (blank($email)) {
            return redirect()
                ->route('affiliate.login')
                ->withErrors(['email' => ucfirst($provider).' did not share an email address. Please use email and password instead.']);
        }

        $affiliate = Affiliate::query()->where('email', $email)->first();

        if (! $affiliate instanceof Affiliate) {
            return redirect()
                ->route('affiliate.register')
                ->with('social_registration_prefill', [
                    'name' => trim((string) $socialUser->getName()),
                    'email' => $email,
                ])
                ->with('status', 'Please complete your affiliate application to continue.');
        }

        if (! $affiliate->hasPermissionTo(Permission::AFFILIATE_DASHBOARD_VIEW_OWN)) {
            return redirect()
                ->route('affiliate.login')
                ->withErrors(['email' => 'This account does not have access to the affiliate portal.']);
        }

        if (blank($affiliate->email_verified_at)) {
            $affiliate->forceFill(['email_verified_at' => now()]);
        }

        $affiliate->forceFill(['last_login_at' => now()])->save();

        Auth::guard('member')->logout();
        Auth::guard('affiliate')->login($affiliate, true);
        $request->session()->regenerate();
        $request->session()->forget('affiliate.pending-review-notice-shown');
        $request->session()->forget('affiliate.approved-welcome-notice-shown');
        $request->session()->forget('url.intended');

        return redirect()->route('affiliate.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('affiliate')->logout();
        $request->session()->forget('affiliate.pending-review-notice-shown');
        $request->session()->forget('affiliate.approved-welcome-notice-shown');
        $request->session()->regenerateToken();

        return redirect()->route('affiliate.landing');
    }

    private function socialProviderIsSupported(string $provider): bool
    {
        return in_array($provider, ['google'], true);
    }

    private function socialProviderIsConfigured(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"));
    }

    private function socialRedirectUrl(string $provider): string
    {
        return (string) (config("services.{$provider}.affiliate_redirect")
            ?: route('affiliate.social.callback', $provider));
    }
}
