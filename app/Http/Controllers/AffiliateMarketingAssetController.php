<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateMarketingAssetType;
use App\Models\AffiliateMarketingAsset;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AffiliateMarketingAssetController extends Controller
{
    public function index(Request $request): View
    {
        $affiliate = $request->user('affiliate');
        abort_if($affiliate->isRejected() || $affiliate->isSuspended(), 403);
        abort_if($affiliate->isApproved() && ! $affiliate->hasPermissionTo(Permission::AFFILIATE_MARKETING_ASSET_VIEW_OWN), 403);
        $validated = $request->validate(['type' => ['nullable', Rule::in([
            AffiliateMarketingAssetType::Image->value,
            AffiliateMarketingAssetType::Video->value,
            AffiliateMarketingAssetType::Document->value,
        ])]]);
        $assets = collect();

        if ($affiliate->isApproved()) {
            $assets = AffiliateMarketingAsset::query()->available()
                ->when($validated['type'] ?? null, fn ($query, $type) => $query->where('asset_type', $type))
                ->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('updated_at')->get();
        }

        return view('pages.affiliate.marketing-materials', compact('affiliate', 'assets') + ['selectedType' => $validated['type'] ?? null]);
    }

    public function download(AffiliateMarketingAsset $asset): StreamedResponse
    {
        Gate::forUser(Auth::guard('affiliate')->user())->authorize('view', $asset);
        abort_unless($asset->file_path && Storage::disk('local')->exists($asset->file_path), 404);

        return Storage::disk('local')->download($asset->file_path, $asset->file_name ?: basename($asset->file_path));
    }

    public function preview(AffiliateMarketingAsset $asset): BinaryFileResponse
    {
        Gate::forUser(Auth::guard('affiliate')->user())->authorize('view', $asset);
        $path = $asset->thumbnail_path ?: $asset->file_path;
        abort_unless($path && Storage::disk('local')->exists($path) && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true), 404);

        return response()->file(Storage::disk('local')->path($path), ['Cache-Control' => 'private, max-age=3600']);
    }
}
