<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MembershipProfileController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $member = auth('member')->user();

        if (! $member instanceof Member) {
            return redirect()
                ->route('membership.login');
        }

        $page = Page::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->where('slug', 'membership-edit-profile')
                    ->orWhere('slug', 'edit-profile')
                    ->orWhere('title', 'like', '%Edit Profile%');
            })
            ->first();

        return view('pages.membership.profile-edit', [
            'member' => $member,
            'page' => $page,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $member = auth('member')->user();

        if (! $member instanceof Member) {
            return redirect()
                ->route('membership.login');
        }

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:150'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'country' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));
        $name = trim((string) ($validated['name'] ?? ''));

        if ($name === '') {
            $name = trim($firstName . ' ' . $lastName);
        }

        $data = [
            'first_name' => $firstName !== '' ? $firstName : null,
            'last_name' => $lastName !== '' ? $lastName : null,
            'name' => $name !== '' ? $name : $member->name,
            'phone_number' => $validated['phone_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'country' => $validated['country'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        if ($request->hasFile('profile_photo')) {
            $oldPhoto = $member->profile_photo ?? $member->photo ?? null;

            $data['profile_photo'] = $request
                ->file('profile_photo')
                ->store('members/profile-photos', 'public');

            if (
                $oldPhoto
                && ! str_starts_with((string) $oldPhoto, 'http://')
                && ! str_starts_with((string) $oldPhoto, 'https://')
                && Storage::disk('public')->exists($oldPhoto)
            ) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        $member->forceFill($data)->save();

        return redirect()
            ->route('membership.dashboard')
            ->with('success', 'Your profile has been updated successfully.');
    }
}
