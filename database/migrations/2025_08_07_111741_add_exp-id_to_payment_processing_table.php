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
        Schema::table('payment_processing', function (Blueprint $table) {
            $table->string('expenditure_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_processing', function (Blueprint $table) {
            $table->dropColumn([
                'expenditure_id'
            ]);
        });
    }
};
