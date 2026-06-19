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
        //
        Schema::create('document_views', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('doc_id');
    $table->unsignedBigInteger('user_id');
    $table->timestamp('viewed_at');
    $table->timestamps();
    
    $table->foreign('doc_id')->references('id')->on('document_approvals')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->unique(['doc_id', 'user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
