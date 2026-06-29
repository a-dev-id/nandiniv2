@php
    $titles = \App\Support\InquiryOptions::titles();
    $countries = \App\Support\InquiryOptions::countries();
    $phoneCodes = \App\Support\InquiryOptions::phoneCodes();
@endphp

<div
    x-data="inquiryModal"
    x-cloak
    x-show="isOpen"
    x-on:keydown.escape.window="close()"
    class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="inquiry-modal-title"
>
    <div x-show="isOpen" x-transition.opacity class="absolute inset-0 bg-black/60"></div>

    <div
        x-show="isOpen"
        x-transition
        class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto bg-white p-6 shadow-2xl sm:p-8"
    >
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase text-[#A88444] sm:text-sm">Inquiry</p>
                <h2 id="inquiry-modal-title" class="text-lg mt-2 uppercase text-slate-700 mb-3 sm:text-xl">Send Inquiry</h2>
            </div>

            <button type="button" class="text-2xl leading-none text-slate-400 transition hover:text-slate-700 sm:text-3xl" x-on:click="close()" aria-label="Close inquiry form">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('inquiries.store') }}" x-on:submit.prevent="submit($event)" class="space-y-5">
            @csrf

            <input type="hidden" name="source_url" x-bind:value="sourceUrl">
            <input type="hidden" name="inquiry_title" x-bind:value="itemTitle">
            <input type="hidden" name="inquiry_image" x-bind:value="itemImage">

            <div x-show="itemTitle" class="border border-[#E5DDCF] bg-[#F8F4EC] px-4 py-3">
                <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#A88444] sm:text-xs">
                    Inquiry For
                </p>
                <p class="m-0 mt-1 text-sm font-medium leading-6 text-slate-700 sm:text-base" x-text="itemTitle"></p>
            </div>

            <template x-if="message">
                <div class="border border-green-200 bg-green-50 px-4 py-3 text-xs text-green-800 sm:text-sm">
                    <p class="m-0" x-text="message"></p>
                    <p x-show="closeCountdown > 0" class="m-0 mt-1 text-xs text-green-700">
                        This popup will close in <span x-text="closeCountdown"></span> seconds.
                    </p>
                </div>
            </template>

            <template x-if="error">
                <div class="border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700 sm:text-sm" x-text="error"></div>
            </template>

            <div class="grid gap-4 sm:grid-cols-3">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Title</span>
                    <select name="title" required class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                        <option value="">Select</option>
                        @foreach ($titles as $title)
                        <option value="{{ $title }}">{{ $title }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">First Name</span>
                    <input type="text" name="first_name" required autocomplete="given-name" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Last Name</span>
                    <input type="text" name="last_name" required autocomplete="family-name" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Email</span>
                    <input type="email" name="email" required autocomplete="email" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Country</span>
                    <select name="country" required autocomplete="country-name" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                        <option value="">Select country</option>
                        @foreach ($countries as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div>
                <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Phone/WA</span>
                <div class="grid grid-cols-[minmax(145px,42%)_1fr] gap-3 sm:grid-cols-[240px_1fr]">
                    <select name="phone_code" required class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                        <option value="">Code</option>
                        @foreach ($phoneCodes as $phoneCode)
                        <option value="{{ $phoneCode['code'] }}" @selected($phoneCode['country'] === 'Indonesia')>{{ $phoneCode['label'] }}</option>
                        @endforeach
                    </select>

                    <input type="tel" name="phone" required autocomplete="tel" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Reserve Date</span>
                    <input type="date" name="reserve_date" required x-bind:min="today" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                </label>

                <label class="block">
                    <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Reserve Time</span>
                    <input type="time" name="reserve_time" required x-bind:min="isLateActivity ? '16:00' : null" x-model="reserveTime" class="w-full border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm">
                    <span x-show="isLateActivity" class="mt-1 block text-xs leading-5 text-slate-500 sm:text-sm">
                        Dinner and night activities start after 16:00.
                    </span>
                </label>
            </div>

            <label class="block">
                <span class="mb-1 block text-xs font-semibold uppercase text-slate-600 sm:text-sm">Note</span>
                <textarea name="note" rows="5" class="w-full resize-y border border-slate-300 px-3 py-3 text-xs text-slate-700 focus:border-[#A88444] focus:outline-none sm:text-sm"></textarea>
            </label>

            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="close()" x-bind:disabled="isSubmitting" class="inline-flex items-center justify-center bg-red-600 px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60 tracking-[0.08em] sm:text-sm">
                    Cancel
                </button>

                <button type="submit" x-bind:disabled="isSubmitting" class="inline-flex items-center justify-center gap-3 bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-[#B8945B] disabled:cursor-not-allowed disabled:opacity-75 tracking-[0.08em] sm:text-sm">
                    <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'Sending' : 'Submit'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
