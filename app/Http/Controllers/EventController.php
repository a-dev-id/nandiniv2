<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Models\Event;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $page = Page::query()
            ->whereKey(43)
            ->where('is_active', true)
            ->firstOrFail();

        /** @var Collection<int, Event> $events */
        $events = Event::query()
            ->published()
            ->orderBy('event_start_at')
            ->orderBy('id')
            ->get();

        $today = today();
        $todayEvent = $events
            ->filter(fn (Event $event): bool => $event->event_type !== EventType::Regular)
            ->filter(fn (Event $event): bool => $event->occursOn($today))
            ->sortBy(fn (Event $event): string => $event->event_start_at->format('H:i:s'))
            ->first();

        $upcomingEvents = $events
            ->filter(fn (Event $event): bool => $event->event_type !== EventType::Regular)
            ->filter(fn (Event $event): bool => $event->event_start_at?->gt($today->copy()->endOfDay()) === true)
            ->take(3)
            ->values();

        $regularEvents = $events
            ->filter(fn (Event $event): bool => $event->event_type === EventType::Regular
                || ! $event->event_start_at
                || $event->event_start_at->lte($today->copy()->endOfDay()))
            ->when(
                $todayEvent,
                fn (Collection $events): Collection => $events->where('id', '!=', $todayEvent->id),
            )
            ->values();

        return view('pages.events', [
            'page' => $page,
            'todayEvent' => $todayEvent,
            'upcomingEvents' => $upcomingEvents,
            'regularEvents' => $regularEvents,
        ]);
    }
}
