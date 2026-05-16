<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('device_id')->nullable()->after('is_admin');
            $table->string('device_name')->nullable()->after('device_id');
            $table->string('device_model')->nullable()->after('device_name');
            $table->timestamp('last_seen_at')->nullable()->after('device_model');
        });

        Schema::table('messages', function (Blueprint $table): void {
            $table->string('sync_status')->default('synced')->after('direction');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropColumn('sync_status');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['device_id', 'device_name', 'device_model', 'last_seen_at']);
        });
    }
};
