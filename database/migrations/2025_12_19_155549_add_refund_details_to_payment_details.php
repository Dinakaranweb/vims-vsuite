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
        Schema::table('payment_details', function (Blueprint $table) {
            $table->string('bill_amount')->nullable();
            $table->string('refund_amount')->nullable();
            $table->date('bill_submission_date')->nullable();
            $table->date('refund_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            $table->dropColumn('bill_amount');
            $table->dropColumn('refund_amount');
            $table->dropColumn('bill_submission_date');
            $table->dropColumn('refund_date');
        });
    }
};
