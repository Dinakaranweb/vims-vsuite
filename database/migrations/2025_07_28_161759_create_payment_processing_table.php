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
        Schema::create('payment_processing', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('doc_id'); // Foreign key to the posts table
            $table->string('assigned_to')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('doc_id')->references('id')->on('document_approvals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_processing');
    }
};
