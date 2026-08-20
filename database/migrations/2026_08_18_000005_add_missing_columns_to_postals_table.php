<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postals', function (Blueprint $table) {
            $table->string('registrar_id')->nullable()->after('post_id');
            $table->string('type_to')->nullable()->after('type');
            $table->string('collected_by')->nullable()->after('closed_by');
            $table->text('remarks')->nullable()->after('category');
            $table->timestamp('due_date')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('postals', function (Blueprint $table) {
            $table->dropColumn(['registrar_id', 'type_to', 'collected_by', 'remarks', 'due_date']);
        });
    }
};
