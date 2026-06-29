<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSectionImage extends Model
{
    protected $fillable = [
        'page_section_id',
        'image',
        'image_file_name',
        'image_alt',
        'mobile_image',
        'mobile_image_file_name',
        'mobile_image_alt',
        'caption',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }
}
