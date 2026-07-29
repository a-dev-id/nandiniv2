<?php

namespace App\Http\Controllers;

use App\Models\GuestReview;
use Illuminate\View\View;

class GuestReviewController extends Controller
{
    public function index(): View
    {
        $reviews = GuestReview::query()
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->paginate(8);

        return view('pages.guest-reviews.index', [
            'reviews' => $reviews,
        ]);
    }
}
