<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('activation_code', 24)->nullable()->unique()->after('email');
            $table->timestamp('activated_at')->nullable()->after('activation_code');
            $table->string('device_brand')->nullable()->after('device_model');
            $table->string('device_manufacturer')->nullable()->after('device_brand');
            $table->string('android_version')->nullable()->after('device_manufacturer');
            $table->string('sdk_int')->nullable()->after('android_version');
            $table->string('device_hardware')->nullable()->after('sdk_int');
            $table->string('device_board')->nullable()->after('device_hardware');
            $table->string('device_product')->nullable()->after('device_board');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_activation_code_unique');
            $table->dropColumn([
                'activation_code',
                'activated_at',
                'device_brand',
                'device_manufacturer',
                'android_version',
                'sdk_int',
                'device_hardware',
                'device_board',
                'device_product',
            ]);
        });
    }
};
