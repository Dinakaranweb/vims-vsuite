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
            $table->string('justification')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('purchase_committee_report')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn('justification');
            $table->dropColumn('purchase_order');
            $table->dropColumn('purchase_committee_report');
        });
    }
};
