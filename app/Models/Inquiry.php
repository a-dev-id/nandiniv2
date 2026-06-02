<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'country',
        'phone_code',
        'phone',
        'note',
        'inquiry_title',
        'inquiry_image',
        'reserve_date',
        'reserve_time',
        'source_url',
        'ip_address',
        'user_agent',
        'submitted_at',
        'email_sent_at',
        'email_error',
        'is_read',
    ];

    protected $casts = [
        'reserve_date' => 'date',
        'submitted_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->title . ' ' . $this->first_name . ' ' . $this->last_name);
    }

    public function getPhoneWaAttribute(): string
    {
        return trim($this->phone_code . ' ' . $this->phone);
    }
}
