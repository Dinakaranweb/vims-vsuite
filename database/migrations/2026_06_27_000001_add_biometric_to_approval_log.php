<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_log', function (Blueprint $table) {
            $table->boolean('biometric_verified')->default(false)->after('message');
            $table->string('platform', 20)->nullable()->after('biometric_verified'); // 'android' | 'ios' | 'web'
            $table->string('device_info', 255)->nullable()->after('platform');
        });
    }

    public function down(): void
    {
        Schema::table('approval_log', function (Blueprint $table) {
            $table->dropColumn(['biometric_verified', 'platform', 'device_info']);
        });
    }
};
