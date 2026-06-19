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
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->string('payment_mode')->nullable();
            $table->string('cash_in_favour')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('account_branch')->nullable();
            $table->string('upi_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'payment_mode',
                'cash_in_favour',
                'account_holder',
                'account_number',
                'ifsc_code',
                'account_branch',
                'upi_id'
            ]);
        });
    }
};
