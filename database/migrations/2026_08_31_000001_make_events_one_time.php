<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('events')
            ->whereIn('event_type', ['weekly', 'monthly', 'yearly'])
            ->update(['event_type' => 'regular']);
    }

    public function down(): void
    {
        // Previous recurrence types cannot be reconstructed without guessing.
    }
};
