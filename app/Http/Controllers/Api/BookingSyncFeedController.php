<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebhotelierReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class BookingSyncFeedController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $token = (string) config('services.membership_api.token');
        $requestToken = (string) $request->bearerToken();

        if ($token === '' || ! hash_equals($token, $requestToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $since = $this->parseSince($request->query('since'));

        $bookings = WebhotelierReservation::query()
            ->when($since, function ($query) use ($since): void {
                $query->where(function ($query) use ($since): void {
                    $query
                        ->where('last_received_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since)
                        ->orWhere('created_at', '>=', $since);
                });
            })
            ->orderBy('last_received_at')
            ->orderBy('id')
            ->get()
            ->map(fn (WebhotelierReservation $reservation): array => $this->mapReservation($reservation))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Booking sync feed returned bookings.',
            'bookings' => $bookings,
        ]);
    }

    private function parseSince(mixed $value): ?Carbon
    {
        try {
            return filled($value) ? Carbon::parse((string) $value) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function mapReservation(WebhotelierReservation $reservation): array
    {
        $guestName = trim((string) $reservation->guest_first_name . ' ' . (string) $reservation->guest_last_name);

        return [
            'booking_number' => (string) $reservation->webhotelier_id,
            'guest_name' => $guestName !== '' ? $guestName : null,
            'email' => $reservation->guest_email,
            'phone' => $reservation->guest_phone,
            'check_in' => $reservation->checkin_date?->toDateString(),
            'check_out' => $reservation->checkout_date?->toDateString(),
            'rooms' => $reservation->rooms,
            'room_type' => $reservation->room_type,
            'room_name' => $reservation->room_name,
            'rate_name' => $reservation->rate_name,
            'currency' => $reservation->currency,
            'booking_total' => $reservation->booking_total,
            'status' => $reservation->status_code,
            'source_name' => $reservation->source_name,
            'created_at' => $reservation->created_at?->toDateTimeString(),
            'remote_updated_at' => $reservation->last_received_at?->toDateTimeString()
                ?? $reservation->updated_at?->toDateTimeString(),
        ];
    }
}
