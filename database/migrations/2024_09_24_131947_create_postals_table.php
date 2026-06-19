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
        Schema::create('postals', function (Blueprint $table) {
            $table->id();
            
            $table->string('post_id');
            $table->string('post_from_address');
            $table->string('post_to_address');
            $table->string('subject');
            $table->string('sent_by');
            $table->string('sent_to');
            $table->string('scanned_copy');
            $table->string('status');
            $table->string('dispatched_to');
            $table->string('dispatched_by');
            $table->string('delivered_by');
            $table->string('closed_by');
            $table->boolean('is_responded')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postals');
    }
};
