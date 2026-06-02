<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('accommodations')
            ->where('title', 'Sunrise View Villa')
            ->update(['villa_code' => 'SVV']);

        DB::table('accommodations')
            ->where('title', 'Jungle View Villa')
            ->update(['villa_code' => 'JVV']);

        DB::table('accommodations')
            ->where('title', 'Panoramic Corner Jacuzzi Royal Suite')
            ->update(['villa_code' => 'PJCS']);
    }

    public function down(): void
    {
        DB::table('accommodations')
            ->whereIn('title', [
                'Sunrise View Villa',
                'Jungle View Villa',
                'Panoramic Corner Jacuzzi Royal Suite',
            ])
            ->update(['villa_code' => null]);
    }
};
