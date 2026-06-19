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
        Schema::create('document_approval_forwardings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_id'); // Foreign key to the posts table
            $table->string('forwarded_by');
            $table->string('forwarded_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_approval_forwardings');
    }
};
