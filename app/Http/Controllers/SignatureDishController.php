<?php

namespace App\Http\Controllers;

use App\Models\DiningSetting;
use App\Models\SignatureDish;
use Illuminate\View\View;

class SignatureDishController extends Controller
{
    public function __invoke(string $signatureDish): View
    {
        $dish = SignatureDish::query()
            ->published()
            ->with(['activeSections.images' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->where('slug', $signatureDish)
            ->firstOrFail();

        return view('pages.dining-landing.signature-dish', [
            'dish' => $dish,
            'sections' => $dish->activeSections,
            'diningSettings' => DiningSetting::query()->first(),
        ]);
    }
}
