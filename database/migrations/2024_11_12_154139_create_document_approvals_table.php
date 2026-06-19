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
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();

            $table->string('doc_id');
            $table->string('from');
            $table->string('by');
            $table->string('to');
            $table->string('subject');
            $table->string('description');
            $table->string('approval_status');
            $table->string('status');
            $table->string('attachment');
            $table->string('tags');
            $table->string('reference');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_approvals');
    }
};
