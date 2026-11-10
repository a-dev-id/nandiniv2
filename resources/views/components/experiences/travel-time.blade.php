@props(['experience'])

@if ($experience->travel_time_minutes !== null && $experience->travel_time_minutes > 0)
<p class="text-xs leading-relaxed text-slate-500">
    Approx. {{ $experience->travel_time_minutes }} min from Nandini
</p>
@endif
