<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebhotelierReservation;
use App\Models\WebhotelierWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Throwable;

class WebhotelierReservationController extends Controller
{
    public function store(Request $request, string $secret)
    {
        if (! hash_equals((string) config('services.webhotelier.webhook_secret'), $secret)) {
            return response('Unauthorized', 401);
        }

        if ($request->isMethod('GET')) {
            return response('OK', 200);
        }

        $rawBody = $request->getContent();
        $headers = $request->headers->all();

        try {
            $payload = json_decode($rawBody, true);

            if (! is_array($payload)) {
                $payload = $request->all();
            }

            $validator = Validator::make($payload, [
                'id' => ['required'],
                'type' => ['required', 'string'],
                'data' => ['required', 'array'],
            ]);

            if ($validator->fails()) {
                WebhotelierWebhookLog::create([
                    'source' => 'webhotelier',
                    'event_type' => 'invalid',
                    'property_code' => $request->header('x-wh-property'),
                    'reservation_id' => Arr::get($payload, 'id'),
                    'confirmation_code' => Arr::get($payload, 'id'),
                    'booking_status' => null,
                    'method' => $request->method(),
                    'ip_address' => $request->ip(),
                    'headers' => $headers,
                    'raw_body' => $rawBody,
                    'payload' => $payload,
                    'processing_status' => 'failed',
                    'processing_error' => $validator->errors()->toJson(),
                ]);

                return response('INVALID PAYLOAD', 400);
            }

            $webhotelierId = (string) Arr::get($payload, 'id');
            $eventType = (string) Arr::get($payload, 'type');
            $data = Arr::get($payload, 'data', []);

            $log = WebhotelierWebhookLog::create([
                'source' => 'webhotelier',
                'event_type' => $eventType,
                'property_code' => $this->extractPropertyCode($data, $request),
                'reservation_id' => $webhotelierId,
                'confirmation_code' => $webhotelierId,
                'booking_status' => $this->extractStatusCode($data),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'headers' => $headers,
                'raw_body' => $rawBody,
                'payload' => $payload,
                'processing_status' => 'pending',
            ]);

            $reservation = $this->upsertReservation(
                webhotelierId: $webhotelierId,
                eventType: $eventType,
                data: $data,
                request: $request,
                logId: $log->id
            );

            $log->update([
                'processing_status' => 'stored',
                'processed_at' => now(),
            ]);

            return response('OK', 200);
        } catch (Throwable $e) {
            WebhotelierWebhookLog::create([
                'source' => 'webhotelier',
                'event_type' => 'error',
                'property_code' => $request->header('x-wh-property'),
                'reservation_id' => null,
                'confirmation_code' => null,
                'booking_status' => null,
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'headers' => $headers,
                'raw_body' => $rawBody,
                'payload' => null,
                'processing_status' => 'failed',
                'processing_error' => $e->getMessage(),
            ]);

            return response('ERROR', 500);
        }
    }

    private function upsertReservation(
        string $webhotelierId,
        string $eventType,
        array $data,
        Request $request,
        int $logId
    ): WebhotelierReservation {
        return WebhotelierReservation::updateOrCreate(
            [
                'webhotelier_id' => $webhotelierId,
            ],
            [
                'property_code' => $this->extractPropertyCode($data, $request),

                'event_type' => $eventType,
                'status_code' => $this->extractStatusCode($data),
                'status' => $this->extractBoolean($data, 'status'),

                'offline' => $this->extractBoolean($data, 'offline'),
                'channelstream' => $this->extractBoolean($data, 'channelstream'),

                'guest_email' => $this->extractGuestEmail($data),
                'guest_first_name' => $this->extractGuestFirstName($data),
                'guest_last_name' => $this->extractGuestLastName($data),
                'guest_phone' => $this->extractGuestPhone($data),

                'checkin_date' => Arr::get($data, 'roomStay.from'),
                'checkout_date' => Arr::get($data, 'roomStay.to'),
                'rooms' => Arr::get($data, 'roomStay.rooms'),

                'room_type' => Arr::get($data, 'roomStay.roomType'),
                'room_name' => Arr::get($data, 'roomStay.roomName'),
                'rate_name' => Arr::get($data, 'roomStay.rateName'),

                'currency' => Arr::get($data, 'pricing.currency'),
                'room_subtotal' => $this->toDecimal(Arr::get($data, 'pricing.subTotal')),
                'booking_total' => $this->toDecimal(Arr::get($data, 'pricing.price')),
                'extras_total' => $this->toDecimal(Arr::get($data, 'pricing.extras')),
                'taxes_total' => $this->toDecimal(Arr::get($data, 'pricing.taxes')),

                'source_id' => Arr::get($data, 'bookInfo.source_id'),
                'source_name' => Arr::get($data, 'bookInfo.source'),

                'last_webhook_log_id' => $logId,
                'payload' => $data,
                'last_received_at' => now(),
            ]
        );
    }

    private function extractPropertyCode(array $data, Request $request): ?string
    {
        return $request->header('x-wh-property')
            ?? Arr::get($data, 'property')
            ?? Arr::get($data, 'property_code')
            ?? Arr::get($data, 'hotel_code')
            ?? Arr::get($data, 'hotel.code')
            ?? Arr::get($data, 'property.code');
    }

    private function extractStatusCode(array $data): ?string
    {
        return Arr::get($data, 'statusCode')
            ?? Arr::get($data, 'status_code')
            ?? Arr::get($data, 'booking_status')
            ?? Arr::get($data, 'bookInfo.status')
            ?? null;
    }

    private function extractGuestEmail(array $data): ?string
    {
        return Arr::get($data, 'customer.email')
            ?? Arr::get($data, 'guest.email')
            ?? Arr::get($data, 'booker.email')
            ?? Arr::get($data, 'email')
            ?? null;
    }

    private function extractGuestFirstName(array $data): ?string
    {
        return Arr::get($data, 'customer.firstName')
            ?? Arr::get($data, 'customer.firstname')
            ?? Arr::get($data, 'guest.firstName')
            ?? Arr::get($data, 'guest.firstname')
            ?? Arr::get($data, 'booker.firstName')
            ?? Arr::get($data, 'firstName')
            ?? null;
    }

    private function extractGuestLastName(array $data): ?string
    {
        return Arr::get($data, 'customer.lastName')
            ?? Arr::get($data, 'customer.lastname')
            ?? Arr::get($data, 'guest.lastName')
            ?? Arr::get($data, 'guest.lastname')
            ?? Arr::get($data, 'booker.lastName')
            ?? Arr::get($data, 'lastName')
            ?? null;
    }

    private function extractGuestPhone(array $data): ?string
    {
        return Arr::get($data, 'customer.phone')
            ?? Arr::get($data, 'guest.phone')
            ?? Arr::get($data, 'booker.phone')
            ?? Arr::get($data, 'phone')
            ?? null;
    }

    private function extractBoolean(array $data, string $key): ?bool
    {
        if (! Arr::has($data, $key)) {
            return null;
        }

        return filter_var(Arr::get($data, $key), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    private function toDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) str_replace(',', '', (string) $value);
    }
}
