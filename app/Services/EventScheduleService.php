<?php

namespace App\Services;

use App\Enums\EventType;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class EventScheduleService
{
    /** @return array{checked: int, updated: int} */
    public function sync(?CarbonInterface $date = null): array
    {
        $reference = $date
            ? CarbonImmutable::instance($date)->startOfDay()
            : CarbonImmutable::now(config('app.timezone'))->startOfDay();

        $summary = [
            'checked' => 0,
            'updated' => 0,
        ];

        Event::query()
            ->published()
            ->whereIn('event_type', [
                EventType::Weekly->value,
                EventType::Monthly->value,
                EventType::Yearly->value,
            ])
            ->whereNotNull('event_start_at')
            ->where('event_start_at', '<', $reference)
            ->orderBy('id')
            ->chunkById(100, function ($events) use ($reference, &$summary): void {
                foreach ($events as $event) {
                    $summary['checked']++;

                    $start = CarbonImmutable::instance($event->event_start_at);
                    $end = $event->event_end_at
                        ? CarbonImmutable::instance($event->event_end_at)
                        : null;
                    $durationInSeconds = $end
                        ? max(0, $start->diffInSeconds($end, false))
                        : null;
                    $nextStart = $this->nextOccurrence($start, $event->event_type, $reference);

                    if ($nextStart->equalTo($start)) {
                        continue;
                    }

                    $event->forceFill([
                        'event_start_at' => $nextStart,
                        'event_end_at' => $durationInSeconds === null
                            ? null
                            : $nextStart->addSeconds($durationInSeconds),
                    ])->saveQuietly();

                    $summary['updated']++;
                }
            });

        return $summary;
    }

    private function nextOccurrence(
        CarbonImmutable $start,
        EventType $type,
        CarbonImmutable $reference,
    ): CarbonImmutable {
        $next = $start;

        while ($next->lt($reference)) {
            $next = match ($type) {
                EventType::Weekly => $next->addWeek(),
                EventType::Monthly => $next->addMonthNoOverflow(),
                EventType::Yearly => $next->addYearNoOverflow(),
                EventType::Regular => $next,
            };
        }

        return $next;
    }
}
