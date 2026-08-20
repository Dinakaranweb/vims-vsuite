<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->string('ticket_id')->nullable()->after('doc_id');
            $table->integer('requested_by')->nullable()->after('forwarded_to');
            $table->string('priority')->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn(['ticket_id', 'requested_by', 'priority']);
        });
    }
};
