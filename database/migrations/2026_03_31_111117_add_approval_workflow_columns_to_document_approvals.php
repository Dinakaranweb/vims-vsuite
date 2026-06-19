<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->enum('is_payment_involved', ['Y', 'N'])->default('N')->after('is_purchase');
            $table->json('approval_sequence')->nullable()->after('is_payment_involved');
            $table->integer('current_sequence_index')->default(0)->after('approval_sequence');
        });
    }

    public function down()
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn(['is_payment_involved', 'approval_sequence', 'current_sequence_index']);
        });
    }
};
