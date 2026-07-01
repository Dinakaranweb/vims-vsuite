<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            // Snapshot of the user's bcrypt hash at token-creation time.
            // If the user changes their password the bcrypt hash changes, the snapshot
            // no longer matches, and ApiAuthenticate returns 401 immediately.
            $table->string('password_hash')->nullable()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('api_tokens', function (Blueprint $table) {
            $table->dropColumn('password_hash');
        });
    }
};
