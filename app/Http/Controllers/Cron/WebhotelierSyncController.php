<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Services\WebhotelierPullService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
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

        $status = $webhotelier->configStatus();

        if (! $status['is_configured']) {
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
                    'message' => 'WebHotelier returned an error.',
                    'note' => 'The cron route is working, but WebHotelier did not allow access to /reservation/new.',
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

                $reservationResponse = $webhotelier->getReservation($reservationId);

                if (($reservationResponse['error_code'] ?? null) !== 'OK') {
                    $results[] = [
                        'ok' => false,
                        'reservation_id' => $reservationId,
                        'message' => 'Failed to retrieve reservation.',
                        'response' => $reservationResponse,
                    ];

                    continue;
                }

                $data = $reservationResponse['data'] ?? [];

                /*
                 * IMPORTANT:
                 * For now this only tests/retrieves the reservation.
                 * Do not mark as synced until we save/upsert it into the database.
                 */

                $results[] = [
                    'ok' => true,
                    'reservation_id' => $reservationId,
                    'sync_type' => $pendingReservation['syncType'] ?? null,
                    'timestamp' => $pendingReservation['timestamp'] ?? null,
                    'status' => $data['statusCode'] ?? null,
                    'guest_name' => trim(($data['clientInfo']['firstName'] ?? '') . ' ' . ($data['clientInfo']['lastName'] ?? '')),
                    'guest_email' => $data['clientInfo']['email'] ?? null,
                    'check_in' => $data['roomStay']['from'] ?? null,
                    'check_out' => $data['roomStay']['to'] ?? null,
                    'room_name' => $data['roomStay']['roomName'] ?? null,
                    'total_price' => $data['pricing']['price'] ?? null,
                    'currency' => $data['pricing']['currency'] ?? null,
                ];
            }

            return response()->json([
                'ok' => true,
                'message' => 'WebHotelier sync test completed.',
                'pending_records' => $records,
                'processed' => count($results),
                'results' => $results,
            ]);
        } catch (Throwable $e) {
            $message = $e->getMessage();

            if (
                str_contains($message, 'status code 403')
                || str_contains($message, '"http_code":403')
                || str_contains($message, '403 Forbidden')
                || str_contains($message, 'Client error: `GET')
            ) {
                return response()->json([
                    'ok' => false,
                    'message' => 'WebHotelier API rejected /reservation/new with 403 Forbidden.',
                ], 403);
            }

            return response()->json([
                'ok' => false,
                'message' => $message,
            ], 500);
        }
    }
}
