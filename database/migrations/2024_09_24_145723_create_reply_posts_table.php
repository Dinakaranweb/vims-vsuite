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
        Schema::create('reply_posts', function (Blueprint $table) {
            $table->id();

            $table->string('post_id');
            $table->string('reply_from_address');
            $table->string('reply_to_address');
            $table->string('subject');
            $table->string('reply_by');
            $table->string('reply_to');
            $table->string('scanned_copy');
            $table->string('status');
            $table->string('dispatched_to');
            $table->string('dispatched_by');
            $table->string('delivered_by');
            $table->string('closed_by');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reply_posts');
    }
};
