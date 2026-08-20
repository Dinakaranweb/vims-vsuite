<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracks which department, if any, currently occupies a "Consult Department" slot in the
     * approval_sequence - lets the UI/backend tell that apart from a normal sequential approval
     * step, so the consulted department is offered Acknowledge instead of Approve/Reject/etc.,
     * and the original raiser gets their full action set back once acknowledged.
     */
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->string('consult_department')->nullable()->after('current_sequence_index');
        });
    }

    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn('consult_department');
        });
    }
};
