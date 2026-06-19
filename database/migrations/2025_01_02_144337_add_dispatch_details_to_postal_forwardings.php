<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('postal_forwardings', function (Blueprint $table) {
            $table->string('dispatched_by')->nullable();
            $table->string('collected_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postal_forwardings', function (Blueprint $table) {
            $table->dropColumn('dispatched_by');
            $table->dropColumn('collected_by');
        });
    }
};
