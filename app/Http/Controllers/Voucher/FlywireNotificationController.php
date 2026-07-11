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

        $fingerprint = hash('sha256', implode('|', [
            data_get($payload, 'id', ''),
            data_get($payload, 'event_id', ''),
            data_get($payload, 'payment.id', data_get($payload, 'payment_id', '')),
            $rawBody,
        ]));

        $event = VoucherPaymentEvent::query()->firstOrCreate(
            ['event_fingerprint' => $fingerprint],
            [
                'gateway' => 'flywire',
                'gateway_payment_id' => data_get($payload, 'payment.id', data_get($payload, 'payment_id')),
                'event_type' => data_get($payload, 'type'),
                'gateway_status' => data_get($payload, 'payment.status', data_get($payload, 'status')),
                'signature_valid' => true,
                'payload' => $payload,
            ]
        );

        if ($event->processed_at) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        try {
            DB::transaction(function () use ($event, $payload, $mapper, $issuer): void {
                $orderNumber = data_get($payload, 'external_reference')
                    ?: data_get($payload, 'payment.external_reference')
                    ?: data_get($payload, 'reference');

                $order = VoucherOrder::query()
                    ->where('order_number', $orderNumber)
                    ->orWhere('flywire_payment_id', data_get($payload, 'payment.id'))
                    ->lockForUpdate()
                    ->first();

                $status = (string) (data_get($payload, 'payment.status') ?: data_get($payload, 'status', 'unknown'));

                if ($order) {
                    $order->forceFill([
                        'payment_status' => $mapper->paymentStatus($status),
                        'order_status' => $mapper->shouldIssue($status) ? 'processing' : $order->order_status,
                        'flywire_payment_id' => data_get($payload, 'payment.id', $order->flywire_payment_id),
                        'flywire_payment_reference' => data_get($payload, 'payment.reference', $order->flywire_payment_reference),
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
}
