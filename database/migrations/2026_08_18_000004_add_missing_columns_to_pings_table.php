<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original create_pings_table migration named the counter column 'pings', but the
     * Ping model/PingController have only ever used 'ping_count' - production was renamed
     * accordingly outside of migrations, same pattern as tickets.title_to/title_from.
     */
    public function up(): void
    {
        Schema::table('pings', function (Blueprint $table) {
            $table->renameColumn('pings', 'ping_count');
        });

        Schema::table('pings', function (Blueprint $table) {
            $table->integer('ping_from')->nullable()->after('task_id');
            $table->string('ping_to')->nullable()->after('ping_from');
        });
    }

    public function down(): void
    {
        Schema::table('pings', function (Blueprint $table) {
            $table->dropColumn(['ping_from', 'ping_to']);
        });

        Schema::table('pings', function (Blueprint $table) {
            $table->renameColumn('ping_count', 'pings');
        });
    }
};
