<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * These columns were declared NOT NULL by their original create-table migrations, but the
     * app never reliably provides values for them on insert (e.g. DocumentApprovalController::store()
     * never sets approval_status - it's only set later by the approval handlers). Production was
     * altered to nullable outside of migrations; doing the same here so inserts stop failing with
     * "doesn't have a default value". No doctrine/dbal installed, so this uses raw MODIFY COLUMN
     * instead of Schema::table(...)->change().
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE document_approvals MODIFY reference VARCHAR(255) NULL");
        DB::statement("ALTER TABLE document_approvals MODIFY tags VARCHAR(255) NULL");
        DB::statement("ALTER TABLE document_approvals MODIFY approval_status VARCHAR(255) NULL");

        DB::statement("ALTER TABLE postals MODIFY post_id VARCHAR(255) NULL");
        DB::statement("ALTER TABLE postals MODIFY subject TEXT NULL");
        DB::statement("ALTER TABLE postals MODIFY scanned_copy VARCHAR(255) NULL");
        DB::statement("ALTER TABLE postals MODIFY dispatched_to VARCHAR(255) NULL");
        DB::statement("ALTER TABLE postals MODIFY dispatched_by VARCHAR(255) NULL");
        DB::statement("ALTER TABLE postals MODIFY delivered_by VARCHAR(255) NULL");
        DB::statement("ALTER TABLE postals MODIFY closed_by VARCHAR(255) NULL");

        DB::statement("ALTER TABLE reply_posts MODIFY scanned_copy VARCHAR(255) NULL");

        DB::statement("ALTER TABLE ticket_conversations MODIFY description LONGTEXT NULL");

        DB::statement("ALTER TABLE tickets MODIFY assigned_to VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE document_approvals MODIFY reference VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE document_approvals MODIFY tags VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE document_approvals MODIFY approval_status VARCHAR(255) NOT NULL");

        DB::statement("ALTER TABLE postals MODIFY post_id VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE postals MODIFY subject VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE postals MODIFY scanned_copy VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE postals MODIFY dispatched_to VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE postals MODIFY dispatched_by VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE postals MODIFY delivered_by VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE postals MODIFY closed_by VARCHAR(255) NOT NULL");

        DB::statement("ALTER TABLE reply_posts MODIFY scanned_copy VARCHAR(255) NOT NULL");

        DB::statement("ALTER TABLE ticket_conversations MODIFY description LONGTEXT NOT NULL");

        DB::statement("ALTER TABLE tickets MODIFY assigned_to VARCHAR(255) NOT NULL");
    }
};
