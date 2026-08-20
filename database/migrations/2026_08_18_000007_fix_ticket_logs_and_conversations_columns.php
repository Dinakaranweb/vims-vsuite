<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ticket_logs.title_id was renamed to ticket_id outside of migrations - every
     * TicketController::logEntry() call has only ever written 'ticket_id'. ticket_conversations
     * never had a ticket_id column at all despite the app always inserting one.
     */
    public function up(): void
    {
        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->renameColumn('title_id', 'ticket_id');
        });

        Schema::table('ticket_conversations', function (Blueprint $table) {
            $table->string('ticket_id')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_conversations', function (Blueprint $table) {
            $table->dropColumn('ticket_id');
        });

        Schema::table('ticket_logs', function (Blueprint $table) {
            $table->renameColumn('ticket_id', 'title_id');
        });
    }
};
