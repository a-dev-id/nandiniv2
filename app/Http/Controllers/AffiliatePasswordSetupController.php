<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AffiliatePasswordSetupController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('pages.affiliate.auth.set-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::broker('affiliates')->reset($data, function (Affiliate $affiliate, string $password): void {
            $affiliate->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($affiliate));
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('affiliate.login')->with('status', 'Your password has been set. You can now sign in.');
    }
}
