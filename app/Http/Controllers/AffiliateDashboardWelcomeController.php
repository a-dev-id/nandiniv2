<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AffiliateDashboardWelcomeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'do_not_show_again' => ['required', 'accepted'],
        ]);

        $affiliate = Auth::guard('affiliate')->user();

        Gate::forUser($affiliate)->authorize('view', $affiliate);
        abort_unless($affiliate->isApproved(), 403);

        if ($affiliate->dashboard_welcome_dismissed_at === null) {
            $affiliate->forceFill(['dashboard_welcome_dismissed_at' => now()])->save();
        }

        return redirect()->route('affiliate.dashboard');
    }
}
