<?php

namespace App\Models;

use App\Support\MemberBookingVoucher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class MiniPopup extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'image_alt',
        'button_label',
        'button_link_type',
        'button_url',
        'button_route',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getResolvedButtonUrlAttribute(): string
    {
        if ($this->button_link_type === 'route' && filled($this->button_route)) {
            return Route::has($this->button_route)
                ? route($this->button_route)
                : '#';
        }

        return filled($this->button_url) ? MemberBookingVoucher::appendToUrl($this->button_url) : '#';
    }
}
