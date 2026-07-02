<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Page;
use App\Models\Accommodation;
use App\Rules\Recaptcha;
use App\Services\MembershipEmailRelayService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Throwable;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MembershipAuthController extends Controller
{
    public function showLoginForm(): View
    {
        $page = Page::query()
            ->where('id', 36)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.membership.auth.sign-in', [
            'page' => $page,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => Recaptcha::rules(),
        ]);
        unset($credentials['g-recaptcha-response']);

        $remember = $request->boolean('remember');

        if (! Auth::guard('member')->attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.',
                ])
                ->onlyInput('email');
        }

        $member = Auth::guard('member')->user();

        if ($member instanceof Member && $this->manualMemberNeedsEmailVerification($member)) {
            Auth::guard('member')->logout();

            return back()
                ->withErrors([
                    'email' => 'Please verify your email address before signing in.',
                ])
                ->onlyInput('email');
        }

        if ($member instanceof Member) {
            $member->applyYearlyTierDowngrade();
        }

        $request->session()->regenerate();

        if ($member instanceof Member && $member->must_change_password) {
            return redirect()->route('membership.password.change');
        }

        return redirect()->route('membership.dashboard');
    }

    public function redirectToSocialProvider(string $provider): RedirectResponse
    {
        abort_unless($this->socialProviderIsSupported($provider), 404);

        if (! $this->socialProviderIsConfigured($provider)) {
            return redirect()
                ->route('membership.login')
                ->withErrors([
                    'email' => ucfirst($provider) . ' sign in is not configured yet.',
                ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleSocialProviderCallback(Request $request, string $provider): RedirectResponse
    {
        abort_unless($this->socialProviderIsSupported($provider), 404);

        if (! $this->socialProviderIsConfigured($provider)) {
            return redirect()
                ->route('membership.login')
                ->withErrors([
                    'email' => ucfirst($provider) . ' sign in is not configured yet.',
                ]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('membership.login')
                ->withErrors([
                    'email' => 'Unable to sign in with ' . ucfirst($provider) . '. Please try again.',
                ]);
        }

        $email = strtolower((string) $socialUser->getEmail());

        if (blank($email)) {
            return redirect()
                ->route('membership.login')
                ->withErrors([
                    'email' => ucfirst($provider) . ' did not share an email address. Please use email and password instead.',
                ]);
        }

        $member = Member::query()
            ->where('email', $email)
            ->first();

        if (! $member instanceof Member) {
            [$firstName, $lastName] = $this->splitSocialName((string) $socialUser->getName());

            return redirect()
                ->route('membership.register')
                ->with('social_registration_prefill', [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                ])
                ->with('status', 'Please complete your membership registration to continue.');
        }

        if (blank($member->email_verified_at)) {
            $member->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        $member->applyYearlyTierDowngrade();

        Auth::guard('member')->login($member, true);
        $request->session()->regenerate();

        if ($member->must_change_password) {
            return redirect()->route('membership.password.change');
        }

        return redirect()->route('membership.dashboard');
    }

    public function showForgotPasswordForm(): View
    {
        $page = Page::query()
            ->where('id', 36)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.membership.auth.forgot-password', [
            'page' => $page,
        ]);
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'g-recaptcha-response' => Recaptcha::rules(),
        ]);

        $status = Password::broker('members')->sendResetLink([
            'email' => strtolower($validated['email']),
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'We have emailed your password reset link.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'We could not find a membership account with that email address.',
            ]);
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        $page = Page::query()
            ->where('id', 36)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.membership.auth.reset-password', [
            'page' => $page,
            'email' => $request->query('email', ''),
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => Recaptcha::rules(),
        ]);

        $status = Password::broker('members')->reset(
            [
                'email' => strtolower($validated['email']),
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $validated['token'],
            ],
            function (Member $member, string $password): void {
                $member->forceFill([
                    'password' => $password,
                    'must_change_password' => false,
                ])->save();

                event(new PasswordReset($member));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('membership.login')
                ->with('status', 'Your password has been reset. You can now sign in.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $status === Password::INVALID_TOKEN
                    ? 'This password reset link is invalid or has expired.'
                    : 'We could not reset your password. Please request a new reset link.',
            ]);
    }

    public function showRegisterForm(): View
    {
        $page = Page::query()
            ->where('id', 38)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.membership.auth.join', [
            'page' => $page,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email'),
            ],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'marketing_consent' => ['nullable', 'boolean'],
            'g-recaptcha-response' => Recaptcha::rules(),
        ]);

        $member = Member::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => strtolower($validated['email']),
            'phone_number' => $validated['phone_number'] ?? null,
            'country' => $validated['country'],
            'address' => $validated['address'] ?? null,
            'password' => $validated['password'],

            'must_change_password' => false,
            'member_source' => Member::SOURCE_MANUAL_REGISTER,

            'tier' => Member::TIER_BRONZE,
            'points' => 0,
            'membership_started_at' => now(),
            'membership_expires_at' => now()->addYear(),

            'marketing_consent' => $request->boolean('marketing_consent'),
            'email_verified_at' => null,
        ]);

        $this->sendVerificationEmail($member);

        return redirect()
            ->route('membership.login')
            ->with('status', 'Registration successful. Please check your email to verify your account before signing in.');
    }

    public function verifyEmail(Request $request, Member $member, string $hash): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        abort_unless(hash_equals(sha1($member->email), $hash), 403);

        if (! $member->email_verified_at) {
            $member->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        return redirect()
            ->route('membership.login')
            ->with('status', 'Your email has been verified. You can now sign in.');
    }

    public function showChangePasswordForm(): View
    {
        return view('pages.membership.auth.change-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $member = Auth::guard('member')->user();

        if (! $member instanceof Member) {
            return redirect()->route('membership.login');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        if (! Hash::check($validated['current_password'], $member->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ]);
        }

        $member->forceFill([
            'password' => $validated['password'],
            'must_change_password' => false,
        ])->save();

        return redirect()
            ->route('membership.dashboard')
            ->with('status', 'Your password has been updated successfully.');
    }

    public function dashboard(): View|RedirectResponse
    {
        $member = Auth::guard('member')->user();

        if ($member instanceof Member) {
            if ($this->manualMemberNeedsEmailVerification($member)) {
                Auth::guard('member')->logout();

                return redirect()
                    ->route('membership.login')
                    ->withErrors([
                        'email' => 'Please verify your email address before accessing your dashboard.',
                    ]);
            }

            $member->applyYearlyTierDowngrade();

            if ($member->must_change_password) {
                return redirect()->route('membership.password.change');
            }
        }

        $page = Page::query()
            ->where('id', 37)
            ->where('is_active', true)
            ->firstOrFail();

        $accommodationIds = [3, 4, 5, 6, 7];
        $accommodations = Accommodation::query()
            ->published()
            ->whereIn('id', $accommodationIds)
            ->orderByRaw('FIELD(id, ' . implode(',', $accommodationIds) . ')')
            ->get();

        return view('pages.membership.dashboard', [
            'page' => $page,
            'accommodations' => $accommodations,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();

        $request->session()->forget('url.intended');
        $request->session()->regenerateToken();

        return redirect()->route('membership.login');
    }

    protected function manualMemberNeedsEmailVerification(Member $member): bool
    {
        return $member->member_source === Member::SOURCE_MANUAL_REGISTER
            && blank($member->email_verified_at);
    }

    protected function sendVerificationEmail(Member $member): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'membership.verify.email',
            now()->addHours(24),
            [
                'member' => $member->id,
                'hash' => sha1($member->email),
            ]
        );

        // The relay sends the rendered Blade email so this site never uses SMTP directly.
        $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.verify-email', [
            'member' => $member,
            'verificationUrl' => $verificationUrl,
        ], [
            'to' => $member->email,
            'bcc' => $this->guestBcc(),
            'subject' => 'Verify Your Nandini Inner Circle Email',
        ]);

        if (! $result['success']) {
            Log::warning('Member verification email could not be sent through relay.', [
                'member_id' => $member->id,
                'email' => $member->email,
                'relay_response' => $result,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : [$bcc];
    }

    protected function socialProviderIsSupported(string $provider): bool
    {
        return in_array($provider, ['google'], true);
    }

    protected function socialProviderIsConfigured(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"))
            && filled(config("services.{$provider}.redirect"));
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    protected function splitSocialName(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name, 2) ?: [];

        return [
            $parts[0] ?? null,
            $parts[1] ?? null,
        ];
    }
}
