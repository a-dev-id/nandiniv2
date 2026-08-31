<?php

namespace App\Http\Controllers;

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
            ->publiclyVisible()
            ->orderBy('event_start_at')
            ->orderBy('id')
            ->get();

        $today = today(config('app.timezone'));
        $dishOfTheMonth = $events
            ->where('is_dish_of_month', true)
            ->sortByDesc('updated_at')
            ->first();

        $scheduledEvents = $events->where('is_dish_of_month', false);

        $todayEvent = $scheduledEvents
            ->filter(fn (Event $event): bool => $event->event_start_at?->lte($today->copy()->endOfDay()) === true
                && ($event->event_end_at?->gte($today->copy()->startOfDay()) ?? true))
            ->sortBy(fn (Event $event): string => $event->event_start_at->format('H:i:s'))
            ->first();

        $upcomingEvents = $scheduledEvents
            ->filter(fn (Event $event): bool => $event->event_start_at?->gt($today->copy()->endOfDay()) === true)
            ->take(3)
            ->values();

        $regularEvents = $scheduledEvents
            ->filter(fn (Event $event): bool => ! $event->event_start_at
                || $event->event_start_at->lte($today->copy()->endOfDay()))
            ->when(
                $todayEvent,
                fn (Collection $events): Collection => $events->where('id', '!=', $todayEvent->id),
            )
            ->values();

        return view('pages.events', [
            'page' => $page,
            'todayEvent' => $todayEvent,
            'dishOfTheMonth' => $dishOfTheMonth,
            'upcomingEvents' => $upcomingEvents,
            'regularEvents' => $regularEvents,
        ]);
    }
}
