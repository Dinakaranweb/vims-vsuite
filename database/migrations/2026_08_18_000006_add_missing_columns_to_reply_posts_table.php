<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reply_posts', function (Blueprint $table) {
            $table->string('post_pid')->nullable()->after('post_id');
            $table->string('reply_from')->nullable()->after('reply_by');
            $table->string('reply_type')->after('tracking_id');
            $table->string('vendor')->nullable()->after('reply_type');
        });
    }

    public function down(): void
    {
        Schema::table('reply_posts', function (Blueprint $table) {
            $table->dropColumn(['post_pid', 'reply_from', 'reply_type', 'vendor']);
        });
    }
};
