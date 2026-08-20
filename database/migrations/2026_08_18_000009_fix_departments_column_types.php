<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The original create_departments_table migration declared dept_name/dept_label as
     * integer, but they've only ever held department name strings ('Medical Director',
     * 'ICT', ...) - production has them as varchar(255). No doctrine/dbal installed, so
     * this uses raw MODIFY COLUMN instead of Schema::table(...)->change().
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE departments MODIFY dept_name VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE departments MODIFY dept_label VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE departments MODIFY dept_name INT NOT NULL");
        DB::statement("ALTER TABLE departments MODIFY dept_label INT NOT NULL");
    }
};
