<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inquiries', 'occasion')) {
            Schema::table('inquiries', fn (Blueprint $table) => $table
                ->string('occasion', 100)
                ->nullable()
                ->after('experience_id'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inquiries', 'occasion')) {
            Schema::table('inquiries', fn (Blueprint $table) => $table->dropColumn('occasion'));
        }
    }
};
