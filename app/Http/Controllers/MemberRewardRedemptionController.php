<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberRewardRedemption;
use App\Models\Page;
use App\Models\Reward;
use App\Services\RewardRedemptionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class MemberRewardRedemptionController extends Controller
{
    public function store(
        Request $request,
        Reward $reward,
        RewardRedemptionService $rewardRedemptionService
    ): RedirectResponse {
        $member = auth('member')->user();

        if (! $member instanceof Member) {
            return redirect()
                ->route('membership.login')
                ->with('error', 'Please login as a member before redeeming this reward.');
        }

        $validated = $request->validate([
            'redeem_date' => ['required', 'date', 'after_or_equal:today'],
            'redeem_time' => ['required', 'date_format:H:i'],
            'reward_title' => ['nullable', 'string', 'max:255'],
            'reward_points' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'special_request' => ['nullable', 'string', 'max:1000'],
        ]);

        $redeemDate = Carbon::parse($validated['redeem_date'])->format('d F Y');
        $redeemTime = Carbon::createFromFormat('H:i', $validated['redeem_time'])->format('H:i');
        $notes = implode(PHP_EOL, array_filter([
            'Preferred date: ' . $redeemDate,
            'Preferred time: ' . $redeemTime,
            'Member name: ' . ($member->full_name ?: $member->name ?: '-'),
            'Email: ' . ($member->email ?: '-'),
            'Phone / WhatsApp: ' . ($member->phone_number ?: '-'),
            'Country: ' . ($member->country ?: '-'),
            'Tier: ' . ($member->tier_label ?: '-'),
            'Available points at request: ' . number_format((int) $member->points),
            filled($validated['special_request'] ?? null) ? 'Special request: ' . $validated['special_request'] : null,
            filled($validated['notes'] ?? null) ? 'Notes: ' . $validated['notes'] : null,
        ]));

        try {
            $redemption = $rewardRedemptionService->redeem(
                member: $member,
                reward: $reward,
                notes: $notes
            );

            return redirect()
                ->route('membership.rewards.thank-you', $redemption)
                ->with('success', 'Reward redeemed successfully.');
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to redeem this reward. Please try again.');
        }
    }

    public function thankYou(MemberRewardRedemption $redemption): View|RedirectResponse
    {
        $member = auth('member')->user();

        if (! $member instanceof Member) {
            return redirect()
                ->route('membership.login');
        }

        if ((int) $redemption->member_id !== (int) $member->id) {
            abort(403);
        }

        $page = Page::query()
            ->where('id', 41)
            ->where('is_active', true)
            ->firstOrFail();

        $sections = $this->getPageSections($page);

        return view('pages.membership.reward-thank-you', [
            'page' => $page,
            'sections' => $sections,
            'member' => $member,
            'redemption' => $redemption,
        ]);
    }

    private function getPageSections(Page $page): Collection
    {
        return $page->sections()
            ->where('is_active', true)
            ->with([
                'images' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
