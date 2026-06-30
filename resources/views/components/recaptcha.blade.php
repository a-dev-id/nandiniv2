@php
$siteKey = config('services.recaptcha.site_key');
@endphp

@if ($siteKey)
<div class="space-y-2">
    <div class="g-recaptcha" data-recaptcha-widget data-sitekey="{{ $siteKey }}"></div>

    @error('g-recaptcha-response')
    <p class="text-[12px] leading-6 text-red-600 sm:text-[14px]">
        {{ $message }}
    </p>
    @enderror
</div>

@once
@push('scripts')
<script>
    window.nandiniRenderRecaptchas = function () {
        if (!window.grecaptcha?.render) {
            return;
        }

        document.querySelectorAll('[data-recaptcha-widget]').forEach(function (widget) {
            if (widget.dataset.widgetId) {
                return;
            }

            widget.dataset.widgetId = window.grecaptcha.render(widget, {
                sitekey: widget.dataset.sitekey,
            });
        });
    };

    document.addEventListener('DOMContentLoaded', window.nandiniRenderRecaptchas);
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=nandiniRenderRecaptchas&render=explicit" async defer></script>
@endpush
@endonce
@endif
