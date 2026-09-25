<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckMembershipEmailRequest;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class MembershipEmailLookupController extends Controller
{
    public function __invoke(CheckMembershipEmailRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        $member = Member::query()
            ->select('tier')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        return response()->json([
            'ok' => true,
            'is_member' => $member !== null,
            'member' => $member === null
                ? null
                : [
                    'tier' => $member->tier,
                    'tier_label' => Str::before($member->tier_label, ' / '),
                ],
            'links' => [
                'membership' => route('membership.index'),
                'join' => route('membership.register'),
                'sign_in' => route('membership.login'),
            ],
        ]);
    }
}
