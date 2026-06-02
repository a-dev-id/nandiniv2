<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\WebhotelierReservation;
use App\Services\MemberAutoJoinService;
use App\Services\WebhotelierPullService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class WebhotelierSyncController extends Controller
{
    public function __invoke(string $token, WebhotelierPullService $webhotelier): JsonResponse
    {
        if (! hash_equals((string) config('services.webhotelier.sync_token'), $token)) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $lockHandle = $this->acquireLock();

        if (! $lockHandle) {
            return response()->json([
                'ok' => false,
                'message' => 'WebHotelier sync is already running.',
            ], 429);
        }

        try {
            return $this->runSync($webhotelier);
        } finally {
            $this->releaseLock($lockHandle);
        }
    }

    protected function runSync(WebhotelierPullService $webhotelier): JsonResponse
    {
        $status = $webhotelier->configStatus();

        if (! ($status['is_configured'] ?? false)) {
            return response()->json([
                'ok' => false,
                'message' => 'WebHotelier API is not configured.',
                'config' => $status,
            ], 500);
        }

        try {
            $pendingResponse = $webhotelier->getPendingReservations();

            if (($pendingResponse['error_code'] ?? null) !== 'OK') {
                return response()->json([
                    'ok' => false,
                    'message' => 'WebHotelier returned an error from /reservation/new.',
                    'response' => $pendingResponse,
                ], 500);
            }

            $pendingReservations = Arr::get($pendingResponse, 'data.reservations', []);
            $records = Arr::get($pendingResponse, 'data.records', count($pendingReservations));

            $results = [];

            foreach ($pendingReservations as $pendingReservation) {
                $reservationId = $pendingReservation['id'] ?? null;

                if (! $reservationId) {
                    $results[] = [
                        'ok' => false,
                        'message' => 'Skipped reservation because ID is missing.',
                        'pending' => $pendingReservation,
                    ];

                    continue;
                }

                $results[] = $this->processReservation($webhotelier, $reservationId, $pendingReservation);
            }

            return response()->json([
                'ok' => true,
                'message' => 'WebHotelier cron sync completed.',
                'pending_records' => $records,
                'processed' => count($results),
                'results' => $results,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function processReservation(
        WebhotelierPullService $webhotelier,
        int|string $reservationId,
        array $pendingReservation
    ): array {
        $webhookLogId = null;

        try {
            $reservationResponse = $webhotelier->getReservation($reservationId);

            if (($reservationResponse['error_code'] ?? null) !== 'OK') {
                $webhookLogId = $this->safeCreatePullLog(
                    $reservationId,
                    $pendingReservation,
                    $reservationResponse,
                    [],
                    'failed',
                    'Failed to retrieve reservation from WebHotelier.'
                );

                return [
                    'ok' => false,
                    'reservation_id' => (string) $reservationId,
                    'sync_type' => $pendingReservation['syncType'] ?? null,
                    'webhook_log_id' => $webhookLogId,
                    'saved_to_database' => false,
                    'marked_as_synced' => false,
                    'auto_join_member' => [
                        'created' => false,
                        'skipped' => true,
                        'reason' => 'Reservation was not retrieved.',
                    ],
                    'message' => 'Failed to retrieve reservation.',
                    'response' => $reservationResponse,
                ];
            }

            $data = $reservationResponse['data'] ?? [];

            $webhookLogId = $this->safeCreatePullLog(
                $reservationId,
                $pendingReservation,
                $reservationResponse,
                $data,
                'pending',
                null
            );

            $localReservation = WebhotelierReservation::updateOrCreate(
                [
                    'webhotelier_id' => (string) $reservationId,
                ],
                $this->mapReservationPayload(
                    $reservationId,
                    $pendingReservation,
                    $reservationResponse,
                    $data,
                    $webhookLogId
                )
            );

            $autoJoinMember = $this->autoJoinMemberFromReservation($reservationId, $data);

            $syncResponse = $webhotelier->markReservationAsSynced($reservationId);

            if (($syncResponse['error_code'] ?? null) !== 'OK') {
                $this->safeMarkPullLogAsFailed(
                    $webhookLogId,
                    'Saved locally, but failed to mark as synced on WebHotelier. Response: '
                        . json_encode($syncResponse, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                );

                return [
                    'ok' => false,
                    'reservation_id' => (string) $reservationId,
                    'sync_type' => $pendingReservation['syncType'] ?? null,
                    'local_reservation_id' => $localReservation->id,
                    'webhook_log_id' => $webhookLogId,
                    'saved_to_database' => true,
                    'marked_as_synced' => false,
                    'auto_join_member' => $autoJoinMember,
                    'message' => 'Saved locally, but failed to mark as synced on WebHotelier.',
                    'sync_response' => $syncResponse,
                ];
            }

            $this->safeMarkPullLogAsProcessed($webhookLogId);

            return [
                'ok' => true,
                'reservation_id' => (string) $reservationId,
                'sync_type' => $pendingReservation['syncType'] ?? null,
                'local_reservation_id' => $localReservation->id,
                'webhook_log_id' => $webhookLogId,
                'saved_to_database' => true,
                'marked_as_synced' => true,
                'auto_join_member' => $autoJoinMember,
                'status_code' => Arr::get($data, 'statusCode'),
                'guest_email' => Arr::get($data, 'clientInfo.email'),
                'check_in' => Arr::get($data, 'roomStay.from'),
                'check_out' => Arr::get($data, 'roomStay.to'),
                'room_name' => Arr::get($data, 'roomStay.roomName'),
                'total_price' => Arr::get($data, 'pricing.price'),
                'currency' => Arr::get($data, 'pricing.currency'),
            ];
        } catch (Throwable $e) {
            $this->safeMarkPullLogAsFailed($webhookLogId, $e->getMessage());

            return [
                'ok' => false,
                'reservation_id' => (string) $reservationId,
                'sync_type' => $pendingReservation['syncType'] ?? null,
                'webhook_log_id' => $webhookLogId,
                'saved_to_database' => false,
                'marked_as_synced' => false,
                'auto_join_member' => [
                    'created' => false,
                    'skipped' => true,
                    'reason' => 'Sync failed before member auto join.',
                ],
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function autoJoinMemberFromReservation(int|string $reservationId, array $data): array
    {
        return app(MemberAutoJoinService::class)
            ->autoJoinFromWebhotelierReservation($reservationId, $data);
    }

    protected function safeCreatePullLog(
        int|string $reservationId,
        array $pendingReservation,
        array $reservationResponse,
        array $data,
        string $processingStatus = 'pending',
        ?string $processingError = null
    ): ?int {
        try {
            $logId = DB::table('webhotelier_webhook_logs')->insertGetId([
                'source' => 'pull',
                'event_type' => $pendingReservation['syncType'] ?? null,
                'property_code' => Arr::get($data, 'property') ?: config('services.webhotelier.property_code'),
                'reservation_id' => (string) $reservationId,
                'confirmation_code' => Arr::get($data, 'id') ? (string) Arr::get($data, 'id') : (string) $reservationId,
                'booking_status' => Arr::get($data, 'statusCode'),
                'method' => 'PULL',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'headers' => json_encode([
                    'endpoint' => '/reservation/' . $reservationId,
                    'pending' => $pendingReservation,
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'raw_body' => json_encode($reservationResponse, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'payload' => json_encode($reservationResponse, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'processing_status' => $processingStatus,
                'processing_error' => $processingError,
                'processed_at' => $processingStatus === 'processed' ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return (int) $logId;
        } catch (Throwable $e) {
            return null;
        }
    }

    protected function safeMarkPullLogAsProcessed(?int $webhookLogId): void
    {
        if (! $webhookLogId) {
            return;
        }

        try {
            DB::table('webhotelier_webhook_logs')
                ->where('id', $webhookLogId)
                ->update([
                    'processing_status' => 'processed',
                    'processing_error' => null,
                    'processed_at' => now(),
                    'updated_at' => now(),
                ]);
        } catch (Throwable $e) {
            //
        }
    }

    protected function safeMarkPullLogAsFailed(?int $webhookLogId, string $error): void
    {
        if (! $webhookLogId) {
            return;
        }

        try {
            DB::table('webhotelier_webhook_logs')
                ->where('id', $webhookLogId)
                ->update([
                    'processing_status' => 'failed',
                    'processing_error' => $error,
                    'processed_at' => now(),
                    'updated_at' => now(),
                ]);
        } catch (Throwable $e) {
            //
        }
    }

    protected function mapReservationPayload(
        int|string $reservationId,
        array $pendingReservation,
        array $reservationResponse,
        array $data,
        ?int $webhookLogId = null
    ): array {
        $clientInfo = Arr::get($data, 'clientInfo', []);
        $roomStay = Arr::get($data, 'roomStay', []);
        $pricing = Arr::get($data, 'pricing', []);

        return [
            'webhotelier_id' => (string) $reservationId,
            'property_code' => Arr::get($data, 'property') ?: config('services.webhotelier.property_code'),
            'event_type' => $pendingReservation['syncType'] ?? null,

            'status_code' => Arr::get($data, 'statusCode'),
            'status' => (bool) Arr::get($data, 'status', false),
            'offline' => (bool) Arr::get($data, 'offline', false),
            'channelstream' => (bool) Arr::get($data, 'channelstream', false),

            'guest_email' => Arr::get($clientInfo, 'email'),
            'guest_first_name' => Arr::get($clientInfo, 'firstName'),
            'guest_last_name' => Arr::get($clientInfo, 'lastName'),
            'guest_phone' => Arr::get($clientInfo, 'phone')
                ?: Arr::get($clientInfo, 'telephone')
                ?: Arr::get($clientInfo, 'tel'),

            'checkin_date' => Arr::get($roomStay, 'from'),
            'checkout_date' => Arr::get($roomStay, 'to'),
            'rooms' => (int) Arr::get($roomStay, 'rooms', 1),
            'room_type' => Arr::get($roomStay, 'roomType'),
            'room_name' => Arr::get($roomStay, 'roomName'),
            'rate_name' => Arr::get($roomStay, 'rateName'),

            'currency' => Arr::get($pricing, 'currency'),
            'room_subtotal' => $this->numberOrNull(Arr::get($pricing, 'room')),
            'booking_total' => $this->numberOrNull(Arr::get($pricing, 'price')),
            'extras_total' => $this->sumMoneyValues(Arr::get($data, 'extras', [])),
            'taxes_total' => $this->numberOrNull(Arr::get($pricing, 'tax'))
                ?? $this->numberOrNull(Arr::get($pricing, 'taxes')),

            'source_id' => Arr::get($data, 'source_id'),
            'source_name' => Arr::get($data, 'source'),

            'last_webhook_log_id' => $webhookLogId,
            'payload' => $reservationResponse,
            'last_received_at' => now(),
        ];
    }

    protected function numberOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return null;
        }

        $value = str_replace(',', '', (string) $value);

        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    protected function sumMoneyValues(mixed $items): ?float
    {
        if (! is_array($items) || empty($items)) {
            return null;
        }

        $total = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $amount = $this->numberOrNull(
                $item['amount']
                    ?? $item['price']
                    ?? $item['total']
                    ?? null
            );

            if ($amount !== null) {
                $total += $amount;
            }
        }

        return $total;
    }

    protected function acquireLock()
    {
        $lockPath = storage_path('framework/cache/webhotelier-pull-sync.lock');

        if (! is_dir(dirname($lockPath))) {
            mkdir(dirname($lockPath), 0755, true);
        }

        $handle = fopen($lockPath, 'c');

        if (! $handle) {
            return false;
        }

        if (! flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);

            return false;
        }

        ftruncate($handle, 0);
        fwrite($handle, now()->toDateTimeString());

        return $handle;
    }

    protected function releaseLock($handle): void
    {
        if ($handle) {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
