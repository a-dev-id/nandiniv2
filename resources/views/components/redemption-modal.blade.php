@php
$member = auth('member')->user();
$memberName = $member?->full_name ?: $member?->name ?: 'Inner Circle Member';
$memberEmail = $member?->email ?: '-';
$memberPhone = $member?->phone_number ?: '-';
$memberCountry = $member?->country ?: '-';
$memberTier = $member?->tier_label ?: '-';
$memberPoints = (int) ($member?->points ?? 0);
@endphp

<div
    x-data="redemptionModal"
    x-cloak
    x-show="isOpen"
    x-on:keydown.escape.window="close()"
    class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="redemption-modal-title"
>
    <div x-show="isOpen" x-transition.opacity class="absolute inset-0 bg-black/60"></div>

    <div
        x-show="isOpen"
        x-transition
        class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto bg-white p-6 shadow-2xl sm:p-8"
    >
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase text-[#A88444] sm:text-sm">Inner Circle</p>
                <h2 id="redemption-modal-title" class="text-lg mt-2 uppercase text-slate-700 mb-3 sm:text-xl">Redeem Points</h2>
            </div>

            <button type="button" class="text-2xl leading-none text-slate-400 transition hover:text-slate-700 sm:text-3xl" x-on:click="close()" aria-label="Close redemption form">
                &times;
            </button>
        </div>

        <form method="POST" x-bind:action="actionUrl" class="space-y-5" x-on:submit="isSubmitting = true">
            @csrf

            <input type="hidden" name="reward_title" x-bind:value="rewardTitle">
            <input type="hidden" name="reward_points" x-bind:value="rewardPoints">

            <div class="border border-[#eee8df] bg-[#f6f3ee] px-4 py-4">
                <p class="text-xs font-semibold uppercase text-[#A88444] sm:text-sm">Reward</p>
                <p class="mt-2 text-base font-medium uppercase text-slate-700 sm:text-lg" x-text="rewardTitle || 'Selected Reward'"></p>
                <p class="mt-1 text-xs uppercase text-slate-600 sm:text-sm">
                    <span x-text="rewardPoints"></span> Points
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Name</span>
                    <input type="text" value="{{ $memberName }}" readonly class="w-full border border-slate-200 bg-slate-100 px-3 py-3 text-xs text-slate-700 focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Email</span>
                    <input type="email" value="{{ $memberEmail }}" readonly class="w-full border border-slate-200 bg-slate-100 px-3 py-3 text-xs text-slate-700 focus:outline-none sm:text-sm">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Phone / WhatsApp</span>
                    <input type="text" value="{{ $memberPhone }}" readonly class="w-full border border-slate-200 bg-slate-100 px-3 py-3 text-xs text-slate-700 focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Country</span>
                    <input type="text" value="{{ $memberCountry }}" readonly class="w-full border border-slate-200 bg-slate-100 px-3 py-3 text-xs text-slate-700 focus:outline-none sm:text-sm">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Tier</span>
                    <input type="text" value="{{ $memberTier }}" readonly class="w-full border border-slate-200 bg-slate-100 px-3 py-3 text-xs text-slate-700 focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Available Points</span>
                    <input type="text" value="{{ number_format($memberPoints) }} Points" readonly class="w-full border border-slate-200 bg-slate-100 px-3 py-3 text-xs text-slate-700 focus:outline-none sm:text-sm">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Preferred Date</span>
                    <input type="date" name="redeem_date" required x-bind:min="today" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Preferred Time</span>
                    <input type="time" name="redeem_time" required class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </label>
            </div>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Special Request</span>
                <textarea name="special_request" rows="4" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm" placeholder="Share any special request for your redemption"></textarea>
            </label>

            <div class="border border-[#eee8df] bg-[#f8f6f2] px-4 py-4">
                <p class="text-xs font-semibold uppercase text-[#A88444] sm:text-sm">Terms & Conditions</p>
                <ul class="mt-3 list-disc space-y-2 pl-5 text-xs leading-relaxed text-slate-600 sm:text-sm">
                    <li>Experience redemption is subject to availability on the preferred date and time</li>
                    <li>The voucher is valid for one month from the date of redemption</li>
                    <li>This voucher cannot be used in conjunction with any other offers, promotions, or discounts</li>
                    <li>To enjoy your experience, please present your unique voucher code to our team upon redemption</li>
                    <li>Any voucher not utilized before its expiry date will be considered void and cannot be reinstated</li>
                    <li>Unused experiences, whether in full or in part, are non-refundable and non-transferable for credit</li>
                    <li>Vouchers hold no cash value and cannot be exchanged for cash or monetary compensation</li>
                </ul>
            </div>

            <x-recaptcha />

            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="close()" x-bind:disabled="isSubmitting" class="inline-flex items-center justify-center bg-red-600 px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60 tracking-[0.08em] sm:text-sm">
                    Cancel
                </button>

                <button type="submit" x-bind:disabled="isSubmitting || !actionUrl" class="inline-flex items-center justify-center gap-3 bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-[#B8945B] disabled:cursor-not-allowed disabled:opacity-75 tracking-[0.08em] sm:text-sm">
                    <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'Redeeming' : 'Redeem'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
