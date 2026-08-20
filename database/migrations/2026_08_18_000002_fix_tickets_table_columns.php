<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original create_tickets_table migration named these columns title_to/title_from,
     * but every controller and the Ticket model's $fillable have only ever used
     * ticket_to/ticket_from - the production database was renamed accordingly outside of
     * migrations, and several other columns the app relies on (doc_id, due_date,
     * assigned_by, forwarded_to, closed_by, rating) were added the same way.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('title_to', 'ticket_to');
            $table->renameColumn('title_from', 'ticket_from');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('doc_id')->nullable()->after('ticket_id');
            $table->string('due_date')->nullable()->after('priority');
            $table->string('assigned_by')->nullable()->after('assigned_to');
            $table->string('forwarded_to')->nullable()->after('is_forwarded');
            $table->string('closed_by')->nullable()->after('status');
            $table->integer('rating')->nullable()->after('closed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['doc_id', 'due_date', 'assigned_by', 'forwarded_to', 'closed_by', 'rating']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('ticket_to', 'title_to');
            $table->renameColumn('ticket_from', 'title_from');
        });
    }
};
