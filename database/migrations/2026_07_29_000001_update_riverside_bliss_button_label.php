<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SECTION_TITLE = 'Riverside Bliss: Half-Day Picnic and Wellness Escape at the River';

    public function up(): void
    {
        DB::table('page_sections')
            ->where('page_id', 1)
            ->where('section_key', 'image_overlay_section')
            ->where('title', self::SECTION_TITLE)
            ->update(['button_label' => 'More Details']);
    }

    public function down(): void
    {
        DB::table('page_sections')
            ->where('page_id', 1)
            ->where('section_key', 'image_overlay_section')
            ->where('title', self::SECTION_TITLE)
            ->where('button_label', 'More Details')
            ->update(['button_label' => 'Discover']);
    }
};
