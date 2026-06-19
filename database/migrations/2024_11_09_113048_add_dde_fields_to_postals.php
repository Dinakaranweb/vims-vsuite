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
        Schema::table('postals', function (Blueprint $table) {
            $table->string('dde_payment_mode')->nullable();
            $table->string('dde_paid_amount')->nullable();
            $table->string('dde_dd_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postals', function (Blueprint $table) {
            $table->dropColumn('dde_payment_mode');
            $table->dropColumn('dde_paid_amount');
            $table->dropColumn('dde_dd_number');
        });
    }
};
