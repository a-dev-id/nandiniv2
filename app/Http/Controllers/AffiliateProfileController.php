<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AffiliateProfileController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.affiliate.profile', [
            'affiliate' => Auth::guard('affiliate')->user(),
        ]);
    }
}
