<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $request->session()->regenerate();

        return redirect()->route('membership.dashboard');
    }

    public function showRegisterForm(): View
    {
        return view('pages.membership.auth.join');
    }

    public function dashboard(): View
    {
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
}
