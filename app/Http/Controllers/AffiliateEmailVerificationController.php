<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Services\Affiliate\AffiliateEmailVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateEmailVerificationController extends Controller
{
    public function verify(Request $request, Affiliate $affiliate, string $hash): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless(hash_equals(sha1($affiliate->email), $hash), 403);

        if ($affiliate->email_verified_at === null) {
            $affiliate->forceFill(['email_verified_at' => now()])->save();
        }

        if (Auth::guard('affiliate')->id() === $affiliate->getKey()) {
            Auth::guard('affiliate')->setUser($affiliate->fresh());

            return redirect()->route('affiliate.dashboard')
                ->with('status', 'Your email address has been verified.');
        }

        return redirect()->route('affiliate.login')
            ->with('status', 'Your email address has been verified. You can now sign in.');
    }

    public function resend(Request $request, AffiliateEmailVerificationService $verification): RedirectResponse
    {
        /** @var Affiliate $affiliate */
        $affiliate = Auth::guard('affiliate')->user();

        if ($affiliate->email_verified_at !== null) {
            return back()->with('status', 'Your email address is already verified.');
        }

        $sent = $verification->send($affiliate);

        return back()->with(
            $sent ? 'status' : 'error',
            $sent
                ? 'A new verification link has been sent to your email address.'
                : 'We could not send the verification email. Please try again later.',
        );
    }
}
