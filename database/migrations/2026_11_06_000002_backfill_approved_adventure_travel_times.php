<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Approved travel times only; activity duration is a separate field.
        $approvedMinutes = [
            'ayung-white-water-rafting' => 15,
        ];

        foreach ($approvedMinutes as $slug => $minutes) {
            DB::table('experiences')
                ->where('slug', $slug)
                ->whereIn('experience_category_id', DB::table('experience_categories')
                    ->select('id')->where('slug', 'ubud-jungle-adventures'))
                ->whereNull('travel_time_minutes')
                ->update(['travel_time_minutes' => $minutes]);
        }
    }

    public function down(): void
    {
        // Retain approved content and later CMS edits until the column is rolled back.
    }
};
