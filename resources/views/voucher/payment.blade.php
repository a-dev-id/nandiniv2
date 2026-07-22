@push('meta')
<title>Secure Voucher Payment | Nandini Jungle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <section class="min-h-[70vh] bg-[#F7F7F7] px-6 pb-16 pt-36 md:pb-24">
        <div class="mx-auto max-w-2xl border border-slate-200 bg-white p-7 text-center sm:p-10">
            <p class="text-xs uppercase tracking-[0.12em] text-[#A88444]">Secure Payment</p>
            <h1 class="mt-4 text-2xl uppercase text-slate-700 sm:text-3xl">Complete Your Voucher Purchase</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">The secure payment window should open automatically. If it does not, use the button below.</p>
            <div class="mx-auto mt-7 max-w-sm border-y border-slate-200 py-5 text-sm text-slate-600">
                <div class="flex justify-between gap-4"><span>Order</span><strong class="text-slate-700">{{ $order->order_number }}</strong></div>
                <div class="mt-3 flex justify-between gap-4"><span>Total</span><strong class="text-slate-700">{{ $order->currency }} {{ number_format($order->total_amount) }}</strong></div>
            </div>
            <p class="mt-5 hidden border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" data-flywire-error></p>
            <button type="button" class="mt-7 inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-6 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B] sm:text-sm" data-flywire-pay>Open Secure Payment</button>
        </div>
    </section>

    @push('scripts')
        <script src="https://checkout.flywire.com/flywire-payment.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const button = document.querySelector('[data-flywire-pay]');
                const errorBox = document.querySelector('[data-flywire-error]');
                let opened = false;

                function showError(message) {
                    if (!errorBox) return;
                    errorBox.textContent = message;
                    errorBox.classList.remove('hidden');
                }

                function openFlywire() {
                    if (!window.FlywirePayment) {
                        showError('The payment service could not be loaded. Please refresh the page and try again.');
                        return;
                    }

                    try {
                        const configuration = @json($configuration);
                        configuration.onInvalidInput = function (errors) {
                            const message = Array.isArray(errors)
                                ? errors.map(function (error) { return error.msg; }).filter(Boolean).join(' ')
                                : 'Please check your payment information.';
                            showError(message || 'Please check your payment information.');
                        };

                        window.FlywirePayment.initiate(configuration).render();
                        opened = true;
                    } catch (error) {
                        showError('The secure payment window could not be opened. Please try again.');
                    }
                }

                button?.addEventListener('click', openFlywire);
                window.setTimeout(function () {
                    if (!opened) openFlywire();
                }, 300);
            });
        </script>
    @endpush
</x-layouts.app>
