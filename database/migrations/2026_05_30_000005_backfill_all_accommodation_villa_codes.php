<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $codes = [
            'Panoramic Jungle View Villa' => 'PVV',
            'Presidential Royal Suite' => 'PDRS',
            'Sunrise View Villa' => 'SVV',
            'Jungle View Villa' => 'JVV',
            'Jungle View Royal Suite' => 'JVRS',
            'Panoramic Corner Jacuzzi Royal Suite' => 'PJCS',
            'Private Garden Royal Suite' => 'GVRS',
        ];

        foreach ($codes as $title => $code) {
            DB::table('accommodations')
                ->where('title', $title)
                ->update(['villa_code' => $code]);
        }
    }

    public function down(): void
    {
        //
    }
};
