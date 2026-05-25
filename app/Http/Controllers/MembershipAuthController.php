<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Page;
use App\Notifications\VerifyMemberEmailNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        ]);

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

        $member->notify(new VerifyMemberEmailNotification($member));

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

        return view('pages.membership.dashboard', [
            'page' => $page,
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
}
