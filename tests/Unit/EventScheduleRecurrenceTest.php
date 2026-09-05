<?php

namespace Tests\Unit;

use App\Enums\EventType;
use App\Services\EventScheduleService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class EventScheduleRecurrenceTest extends TestCase
{
    public function test_original_recurrence_rules_and_time_of_day_are_preserved(): void
    {
        $method = new \ReflectionMethod(EventScheduleService::class, 'nextOccurrence');
        $service = new EventScheduleService;

        foreach ([
            [EventType::Weekly, '2026-08-05 19:00:00', '2026-09-05', '2026-09-09 19:00:00'],
            [EventType::Monthly, '2026-01-31 19:00:00', '2026-02-01', '2026-02-28 19:00:00'],
            [EventType::Yearly, '2024-02-29 19:00:00', '2025-01-01', '2025-02-28 19:00:00'],
            [EventType::Weekly, '2026-09-05 19:00:00', '2026-09-05', '2026-09-05 19:00:00'],
        ] as [$type, $start, $reference, $expected]) {
            $result = $method->invoke(
                $service,
                CarbonImmutable::parse($start, 'Asia/Makassar'),
                $type,
                CarbonImmutable::parse($reference, 'Asia/Makassar'),
            );

            $this->assertSame($expected, $result->format('Y-m-d H:i:s'));
            $this->assertSame('Asia/Makassar', $result->timezoneName);
        }
    }
}
