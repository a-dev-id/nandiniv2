<?php

namespace App\Http\Controllers\Voucher;

use App\Http\Controllers\Controller;
use App\Models\VoucherOrder;
use App\Models\VoucherPaymentEvent;
use App\Services\Payments\Flywire\FlywireStatusMapper;
use App\Services\Payments\Flywire\FlywireWebhookVerifier;
use App\Services\Voucher\VoucherIssuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlywireNotificationController extends Controller
{
    public function __invoke(
        Request $request,
        FlywireWebhookVerifier $verifier,
        FlywireStatusMapper $mapper,
        VoucherIssuer $issuer,
    ): JsonResponse {
        $rawBody = $request->getContent();
        $digest = $request->header('X-Flywire-Digest');

        abort_unless($verifier->isValid($rawBody, $digest), 403);

        $payload = json_decode($rawBody, true);
        abort_unless(is_array($payload), 400);

        $status = $this->resolveStatus($payload);

        $fingerprint = hash('sha256', implode('|', [
            data_get($payload, 'id', ''),
            data_get($payload, 'event_id', ''),
            data_get($payload, 'payment.id', data_get($payload, 'data.payment_id', data_get($payload, 'payment_id', ''))),
            $rawBody,
        ]));

        $event = VoucherPaymentEvent::query()->firstOrCreate(
            ['event_fingerprint' => $fingerprint],
            [
                'gateway' => 'flywire',
                'gateway_payment_id' => data_get($payload, 'payment.id', data_get($payload, 'data.payment_id', data_get($payload, 'payment_id'))),
                'event_type' => data_get($payload, 'event_type', data_get($payload, 'type')),
                'gateway_status' => $status,
                'signature_valid' => true,
                'payload' => $payload,
            ]
        );

        if ($event->processed_at) {
            if ($event->voucher_order_id) {
                $orderAlreadyPaid = VoucherOrder::query()
                    ->whereKey($event->voucher_order_id)
                    ->where('payment_status', 'paid')
                    ->exists();

                if ($orderAlreadyPaid || ! $mapper->shouldIssue($status)) {
                    $issuer->deliverUndeliveredForOrder((int) $event->voucher_order_id);

                    return response()->json(['ok' => true, 'duplicate' => true]);
                }
            } else {
                return response()->json(['ok' => true, 'duplicate' => true]);
            }
        }

        try {
            DB::transaction(function () use ($event, $payload, $status, $mapper, $issuer): void {
                $orderNumber = data_get($payload, 'data.external_reference')
                    ?: data_get($payload, 'external_reference')
                    ?: data_get($payload, 'payment.external_reference')
                    ?: data_get($payload, 'reference');

                $paymentId = data_get($payload, 'data.payment_id')
                    ?: data_get($payload, 'payment.id')
                    ?: data_get($payload, 'payment_id');

                $order = VoucherOrder::query()
                    ->where('order_number', $orderNumber)
                    ->orWhere('flywire_payment_id', $paymentId)
                    ->lockForUpdate()
                    ->first();

                if ($order) {
                    $order->forceFill([
                        'payment_status' => $mapper->paymentStatus($status),
                        'order_status' => $mapper->shouldIssue($status) ? 'processing' : $order->order_status,
                        'flywire_payment_id' => $paymentId ?: $order->flywire_payment_id,
                        'flywire_payment_reference' => data_get($payload, 'data.payment_id', data_get($payload, 'payment.reference', $order->flywire_payment_reference)),
                        'flywire_status' => $status,
                    ])->save();

                    $event->voucher_order_id = $order->id;

                    if ($mapper->shouldIssue($status)) {
                        $issuer->issueForOrder($order);
                    }
                }

                $event->forceFill([
                    'gateway_status' => $status,
                    'processed_at' => now(),
                    'processing_error' => null,
                ])->save();
            });
        } catch (\Throwable $e) {
            $event->forceFill(['processing_error' => $e->getMessage()])->save();
            report($e);

            return response()->json(['ok' => false], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Flywire callback version 2 uses event_type for the payment status.
     * Keep the older payload shapes as fallbacks for backwards compatibility.
     *
     * @param  array<string, mixed>  $payload
     */
    private function resolveStatus(array $payload): string
    {
        return (string) (data_get($payload, 'event_type')
            ?: data_get($payload, 'data.status')
            ?: data_get($payload, 'payment.status')
            ?: data_get($payload, 'status', 'unknown'));
    }
}
