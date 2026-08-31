<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\EventType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'excerpt',
        'description',
        'image',
        'image_name',
        'alt_text',
        'status',
        'event_start_at',
        'event_end_at',
        'event_type',
        'schedule_text',
        'is_dish_of_month',
    ];

    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'event_type' => EventType::class,
            'event_start_at' => 'datetime',
            'event_end_at' => 'datetime',
            'is_dish_of_month' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Event $event): void {
            if (! $event->is_dish_of_month) {
                return;
            }

            Event::query()
                ->whereKeyNot($event->getKey())
                ->where('is_dish_of_month', true)
                ->update(['is_dish_of_month' => false]);
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published->value);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        $now = CarbonImmutable::now(config('app.timezone'));

        return $query
            ->published()
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('event_end_at')
                    ->orWhere('event_end_at', '>=', $now);
            });
    }

    public function getScheduleLabelAttribute(): ?string
    {
        if ((! $this->event_start_at || ! $this->event_end_at) && filled($this->schedule_text)) {
            return $this->formattedScheduleText();
        }

        $start = $this->event_start_at;
        $end = $this->event_end_at;

        if (! $start || ! $end) {
            return null;
        }

        $time = $start->format('g:i A').' – '.$end->format('g:i A');

        return $start->isSameDay($end)
            ? $start->format('D, d M Y').' · '.$time
            : $start->format('d M Y, g:i A').' – '.$end->format('d M Y, g:i A');
    }

    public function getUpcomingScheduleLabelAttribute(): ?string
    {
        if (! $this->event_start_at && filled($this->schedule_text)) {
            return $this->formattedScheduleText();
        }

        $start = $this->event_start_at;

        if (! $start) {
            return null;
        }

        $label = $start->format('F jS, Y').'. Start from '.$start->format('g:i A');

        return $this->event_end_at
            ? $label.' - '.$this->event_end_at->format('g:i A')
            : $label;
    }

    public function getTodayScheduleLabelAttribute(): ?string
    {
        if (! $this->event_start_at && filled($this->schedule_text)) {
            return $this->formattedScheduleText();
        }

        if (! $this->event_start_at) {
            return null;
        }

        $label = 'Start from '.$this->event_start_at->format('g:i A');

        return $this->event_end_at
            ? $label.' – '.$this->event_end_at->format('g:i A')
            : $label;
    }

    private function formattedScheduleText(): string
    {
        return (string) preg_replace_callback(
            '/(?<!\d)([01]?\d|2[0-3]):([0-5]\d)(?!\d)(?!\s*(?:AM|PM)\b)/i',
            fn (array $matches): string => CarbonImmutable::createFromTime(
                (int) $matches[1],
                (int) $matches[2],
            )->format('g:i A'),
            (string) $this->schedule_text,
        );
    }

    public function getSummaryAttribute(): string
    {
        $value = filled($this->excerpt) ? $this->excerpt : $this->description;
        $value = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace("\xc2\xa0", ' ', $value);

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }
}
