<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inquiries', 'experience_id')) {
            return;
        }

        // Fresh SQLite test databases already receive the corrected constraint
        // from the preceding migration; information_schema is MySQL-specific.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $referencedTable = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'inquiries')
            ->where('COLUMN_NAME', 'experience_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('REFERENCED_TABLE_NAME');

        if ($referencedTable === 'experiences') {
            return;
        }

        Schema::table('inquiries', function (Blueprint $table) use ($referencedTable): void {
            if ($referencedTable) {
                $table->dropForeign(['experience_id']);
            }
        });

        DB::table('inquiries')->whereNotNull('experience_id')->update(['experience_id' => null]);

        Schema::table('inquiries', fn (Blueprint $table) => $table
            ->foreign('experience_id')
            ->references('id')
            ->on('experiences')
            ->nullOnDelete());
    }

    public function down(): void
    {
        // The former foreign key targeted unrelated landing-page content and is not restored.
    }
};
