<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dde_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // Foreign key to the posts table
            $table->string('c_code')->nullable();
            $table->string('fee_item')->nullable();
            $table->string('mode')->nullable();
            $table->string('payment_reference_no')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('micr_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->string('receipt_no')->nullable();
            $table->date('received_date')->nullable();
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('post_id')->references('id')->on('postals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dde_details');
    }
};
