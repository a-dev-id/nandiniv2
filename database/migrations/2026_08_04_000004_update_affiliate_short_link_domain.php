<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('affiliate_program_settings')
            ->where('short_link_domain', 'nandini.link')
            ->update(['short_link_domain' => 'go.nandinibali.com']);
    }

    public function down(): void
    {
        DB::table('affiliate_program_settings')
            ->where('short_link_domain', 'go.nandinibali.com')
            ->update(['short_link_domain' => 'nandini.link']);
    }
};
