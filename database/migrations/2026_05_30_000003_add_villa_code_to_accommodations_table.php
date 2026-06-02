<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->string('villa_code', 20)->nullable()->after('accommodation_type');
        });

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
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn('villa_code');
        });
    }
};
