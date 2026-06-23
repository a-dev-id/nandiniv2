<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'phone_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:40'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'country' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (filled($validated['password'] ?? null) && filled($member->password)) {
            if (! Hash::check((string) ($validated['current_password'] ?? ''), (string) $member->password)) {
                return back()
                    ->withInput($request->except(['current_password', 'password', 'password_confirmation']))
                    ->withErrors([
                        'current_password' => 'The current password is incorrect.',
                    ]);
            }
        }

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));
        $name = trim($firstName . ' ' . $lastName);

        $data = [
            'first_name' => $firstName !== '' ? $firstName : null,
            'last_name' => $lastName !== '' ? $lastName : null,
            'name' => $name !== '' ? $name : $member->name,
            'phone_number' => filled($validated['phone'] ?? null)
                ? trim((string) ($validated['phone_code'] ?? '') . ' ' . (string) ($validated['phone'] ?? ''))
                : null,
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

        if (filled($validated['password'] ?? null)) {
            $data['password'] = $validated['password'];
            $data['must_change_password'] = false;
        }

        $member->forceFill($data)->save();

        return redirect()
            ->route('membership.dashboard')
            ->with('success', 'Your profile has been updated successfully.');
    }
}
