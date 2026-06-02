<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('inquiry_title')->nullable()->after('note');
            $table->text('inquiry_image')->nullable()->after('inquiry_title');
            $table->date('reserve_date')->nullable()->after('inquiry_image');
            $table->time('reserve_time')->nullable()->after('reserve_date');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'inquiry_title',
                'inquiry_image',
                'reserve_date',
                'reserve_time',
            ]);
        });
    }
};
