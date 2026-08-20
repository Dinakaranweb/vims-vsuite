<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracks when a document's approval flow actually completed and when its creator formally
     * closed it, so "days to close" can be measured instead of only ever being inferable (with
     * effort) from approval_log/document_logs text.
     */
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('current_sequence_index');
            $table->timestamp('closed_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'closed_at']);
        });
    }
};
