<?php

namespace App\Services;

use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;

class MemberStayDateBackfillService
{
    public function fillMissingDatesForMember(Member $member, bool $dryRun = false): bool
    {
        if ($member->booking_check_in && $member->booking_check_out) {
            return false;
        }

        $booking = $member->syncedBookings()
            ->where(function ($query): void {
                $query
                    ->whereNotNull('check_in')
                    ->orWhereNotNull('check_out');
            })
            ->orderByDesc('check_in')
            ->orderByDesc('check_out')
            ->orderByDesc('id')
            ->get()
            ->first(fn (SyncedWebhotelierBooking $booking): bool => ($member->booking_check_in || $booking->check_in)
                && ($member->booking_check_out || $booking->check_out));

        if (! $booking) {
            return false;
        }

        $updates = [];

        if (! $member->booking_check_in && $booking->check_in) {
            $updates['booking_check_in'] = $booking->check_in->toDateString();
        }

        if (! $member->booking_check_out && $booking->check_out) {
            $updates['booking_check_out'] = $booking->check_out->toDateString();
        }

        if ($updates === []) {
            return false;
        }

        if (! $dryRun) {
            $member->forceFill($updates)->save();
        }

        return true;
    }

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

                    if (! $this->fillMissingDatesForMember($member, $dryRun)) {
                        $summary['skipped']++;

                        continue;
                    }

                    $summary['updated']++;
                }
            });

        return $summary;
    }
}
