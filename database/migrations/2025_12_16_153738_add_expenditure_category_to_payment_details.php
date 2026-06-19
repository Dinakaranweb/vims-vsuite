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
            // Add expenditure_category column
            $table->text('expenditure_category')->nullable()->after('expenditure_id');
            
            // Add cheque issue date and cleared date columns
            $table->date('cheque_issue_date')->nullable()->after('payment_date');
            $table->date('cheque_cleared_date')->nullable()->after('cheque_issue_date');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function (Blueprint $table) {
            // Drop the columns if rolling back
            $table->dropColumn('expenditure_category');
            $table->dropColumn('cheque_cleared_date');
            $table->dropColumn('cheque_issue_date');
            
        });
    }
};
