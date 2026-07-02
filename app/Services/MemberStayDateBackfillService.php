<?php

namespace App\Services;

use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;

class MemberStayDateBackfillService
{
    /**
     * @return array{checked: int, updated: int, skipped: int}
     */
    public function backfill(bool $dryRun = false): array
    {
        $summary = [
            'checked' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        Member::query()
            ->where(function ($query): void {
                $query
                    ->whereNull('booking_check_in')
                    ->orWhereNull('booking_check_out');
            })
            ->whereHas('syncedBookings', function ($query): void {
                $query
                    ->whereNotNull('check_in')
                    ->orWhereNotNull('check_out');
            })
            ->with(['syncedBookings' => function ($query): void {
                $query
                    ->where(function ($query): void {
                        $query
                            ->whereNotNull('check_in')
                            ->orWhereNotNull('check_out');
                    })
                    ->orderByDesc('check_in')
                    ->orderByDesc('check_out')
                    ->orderByDesc('id');
            }])
            ->chunkById(100, function ($members) use (&$summary, $dryRun): void {
                foreach ($members as $member) {
                    $summary['checked']++;

                    $booking = $member->syncedBookings
                        ->first(fn (SyncedWebhotelierBooking $booking): bool => ($member->booking_check_in || $booking->check_in)
                            && ($member->booking_check_out || $booking->check_out));

                    if (! $booking) {
                        $summary['skipped']++;

                        continue;
                    }

                    $updates = [];

                    if (! $member->booking_check_in && $booking->check_in) {
                        $updates['booking_check_in'] = $booking->check_in->toDateString();
                    }

                    if (! $member->booking_check_out && $booking->check_out) {
                        $updates['booking_check_out'] = $booking->check_out->toDateString();
                    }

                    if ($updates === []) {
                        $summary['skipped']++;

                        continue;
                    }

                    if (! $dryRun) {
                        $member->forceFill($updates)->save();
                    }

                    $summary['updated']++;
                }
            });

        return $summary;
    }
}
